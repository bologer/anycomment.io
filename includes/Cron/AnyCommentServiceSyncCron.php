<?php

namespace AnyComment\Cron;

use AnyComment\AnyCommentServiceApi;
use AnyComment\Admin\AnyCommentIntegrationSettings;

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
            $token = static::getSyncApiKey();

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
     * Processing syncing of comments.
     *
     * @return bool
     */
    public function sync_comments()
    {
        $comment_id = static::getSyncCommentId();

        if (empty($comment_id)) {
            $comment_id = 0;
        }

        global $wpdb;

        $prepare = $wpdb->prepare("SELECT * FROM {$wpdb->comments} WHERE comment_id > %d LIMIT 5", $comment_id);

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

            $author = [
                'name' => $user->user_nicename,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'avatar' => get_avatar_url($user->ID)
            ];
        } else {
            $author = [
                'name' => $comment->comment_author,
                'email' => !empty($comment->comment_author_email) ?
                    $comment->comment_author_email :
                    null,
                'avatar' => !empty($comment->comment_author_email) ?
                    get_avatar_url($comment->comment_author_email) :
                    null
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

        $resp = AnyCommentServiceApi::request()->post('client/comment/add', $body, ['token' => $this->getSyncApiKey()]);

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
     * Get sync App ID from SaaS.
     *
     * @param null $default
     * @return mixed|void
     */
    public static function getSyncAppId($default = null)
    {
        return get_option(static::getSyncAppIdOptionName(), $default);
    }

    /**
     * Get sync API key from SaaS.
     *
     * @param null $default
     * @return mixed|void
     */
    public static function getSyncApiKey($default = null)
    {
        return get_option(static::getSyncApiKeyOptionName(), $default);
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
    public static function getSyncAppIdOptionName()
    {
        return 'anycomment_sync_app_id';
    }

    /**
     * @return string
     */
    public static function getSyncApiKeyOptionName()
    {
        return 'anycomment_sync_api_key';
    }

    /**
     * @return string
     */
    public static function getSyncCommentIdOptionName()
    {
        return 'anycomment_last_sync_id';
    }

    /**
     * Check whether app id and api key are available for syncing.
     *
     * @return bool
     */
    public static function isSyncReady()
    {
        $app_id = static::getSyncAppId();
        $api_key = static::getSyncApiKey();
        return !empty($app_id) && !empty($api_key);
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
