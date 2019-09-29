<?php

namespace AnyComment\Cron;

use AnyComment\AnyCommentServiceApi;
use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\AnyCommentUserMeta;
use AnyComment\Api\AnyCommentServiceSyncIn;
use AnyComment\Rest\AnyCommentSocialAuth;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AnyCommentServiceSyncCron
{
    /**
     * AnyCommentEmailCron constructor.
     */
    public function __construct()
    {
        if (AnyCommentIntegrationSettings::is_sass_comments_sync()) {
            $token = AnyCommentServiceApi::getSyncApiKey();

            if (!empty($token)) {
                $this->init();
            }
        }
    }

    /**
     * Init class.
     */
    private function init()
    {
        add_filter('cron_schedules', [$this, 'add_minute_interval']);

        if (!wp_next_scheduled('anycomment_service_sync_cron')) {
            wp_schedule_event(time(), 'every_minute', 'anycomment_service_sync_cron');
        }

        add_action('anycomment_service_sync_cron', [$this, 'sync_comments']);
    }

    /**
     * Add new every minute interval.
     *
     * @param array $schedules List of available schedules.
     *
     * @return mixed
     */
    public function add_minute_interval($schedules)
    {
        $schedules['every_minute'] = array(
            'interval' => 60,
            'display' => esc_html__('Every Minute'),
        );

        return $schedules;
    }

    /**
     * Processing two-syncing syncing of comments.
     *
     * @return bool
     */
    public function sync_comments()
    {
        // Sync to the service
        $is_out_success = $this->sync_comments_out();

        // Sync from the service
        $is_in_success = $this->sync_comments_in();

        return $is_out_success && $is_in_success;
    }

    /**
     * Sync comments from the service.
     *
     * @return bool
     */
    public function sync_comments_in()
    {
        return (new AnyCommentServiceSyncIn())->sync();
    }

    /**
     * Send comments to the service.
     *
     * @return bool
     */
    public function sync_comments_out()
    {
        $comment_id = static::getSyncCommentId();

        if (empty($comment_id)) {
            $comment_id = 0;
        }

        global $wpdb;

        $prepare = $wpdb->prepare("SELECT * FROM {$wpdb->comments} WHERE comment_id > %d LIMIT 10", $comment_id);

        $comments = $wpdb->get_results($prepare);

        if (empty($comments)) {
            return false;
        }

        foreach ($comments as $key => $comment) {
            $comment = new \WP_Comment($comment);
            $this->sync_comment($comment);
        }

        return true;
    }

    /**
     * @param \WP_Comment $comment
     * @return bool
     */
    public function sync_comment(\WP_Comment $comment)
    {

        $post = get_post($comment->comment_post_ID);

        if (empty($post)) {
            return false;
        }

        $page_url = get_permalink($post);

        if ($page_url === false) {
            return false;
        }

        $post_thumbnail_url = get_the_post_thumbnail_url($post);
        $page_preview_url = $post_thumbnail_url !== false ? $post_thumbnail_url : null;
        $page_author = get_the_author_meta('user_nicename', $post->post_author);


        if (!empty($comment->user_id)) {
            $user = get_userdata($comment->user_id);

            if ($user === false) {
                return false;
            }

            $profileUrl = null;

            if (($socialUrl = AnyCommentUserMeta::get_social_profile_url($user->ID)) !== null) {
                $profileUrl = $socialUrl;
            } elseif (!empty($user->user_url)) {
                $profileUrl = $user->user_url;
            } elseif (!empty($comment->comment_author_url)) {
                $profileUrl = $comment->comment_author_url;
            }

            $author = [
                'name' => $user->user_nicename,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'avatar' => AnyCommentSocialAuth::get_user_avatar_url($user->ID),
                'url' => $profileUrl
            ];
        } else {
            $profileUrl = null;

            if (!empty($comment->comment_author_url)) {
                $profileUrl = $comment->comment_author_url;
            }

            $author = [
                'name' => $comment->comment_author,
                'email' => !empty($comment->comment_author_email) ?
                    $comment->comment_author_email :
                    null,
                'avatar' => !empty($comment->comment_author_email) ?
                    get_avatar_url($comment->comment_author_email) :
                    null,
                'url' => $profileUrl
            ];


        }

        $body = [
            'page_url' => $page_url,
            'page_title' => $post->post_title,
            'page_preview_url' => $page_preview_url,
            'page_author' => $page_author,
            'comment' => [
                'id' => $comment->comment_ID,
                'parent_id' => $comment->comment_parent > 0 ? $comment->comment_parent : null,
                'status' => $this->getServiceStatus($comment),
                'content' => $comment->comment_content,
                'ip' => $comment->comment_author_IP,
                'created_date' => $comment->comment_date
            ],
            'author' => $author
        ];

        $resp = AnyCommentServiceApi::request()->post(
            'client/comment/add',
            $body,
            ['token' => AnyCommentServiceApi::getSyncApiKey()]
        );

        if (is_wp_error($resp)) {
            return false;
        }

        if (!isset($resp['response']['code'])) {
            return false;
        }

        if ((int)$resp['response']['code'] === 200) {
            $jsonString = wp_remote_retrieve_body($resp);
            $data = json_decode($jsonString, true);

            if (!isset($data['status'])) {
                return false;
            }

            if ($data['status'] === 'fail') {
                return false;
            }

            if (isset($data['response']['id']) && is_int($data['response']['id'])) {
                update_option(self::getSyncCommentIdOptionName(), $comment->comment_ID);
                return true;
            }
        }

        return false;
    }


    /**
     * Get last sync comment id.
     *
     * @param null $default
     * @return mixed|void
     */
    public static function getSyncCommentId($default = null)
    {
        return get_option(static::getSyncCommentIdOptionName(), $default);
    }

    /**
     * Converts WordPress's status to service's one.
     * @param \WP_Comment $comment
     * @return int
     */
    public function getServiceStatus(\WP_Comment $comment)
    {
        switch ($comment->comment_approved) {
            case 'approved' :
            case 'approve':
            case '1':
                return 1;
                break;
            case 'hold':
            case '0':
                return 0;
                break;
            case 'spam' :
                return 2;
                break;
            case 'trash' :
                return 3;
                break;
            default :
                return 1;
                break;
        }
    }

    /**
     * @return string
     */
    public static function getSyncCommentIdOptionName()
    {
        return 'anycomment_last_sync_id';
    }

    /**
     * Returns sync information.
     *
     * @return array
     */
    public static function getSyncInfo()
    {
        global $wpdb;

        $comment_id = intval(static::getSyncCommentId(0));

        $info = [];

        $global_total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments}");

        $info['total'] = $global_total;

        if (empty($comment_id)) {
            $info['complete_percent'] = 0;
            $info['current'] = 0;
            $info['remaining'] = $global_total;
        } else {
            $remaining = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_id > %d",
                $comment_id
            ));

            $remaining = intval($remaining);

            $info['complete_percent'] = $remaining > 0 ?
                round((1 - $remaining / $global_total) * 100, 2) :
                100;
            $info['current'] = $global_total - $remaining;
            $info['remaining'] = $remaining;
        }

        return $info;
    }
}
