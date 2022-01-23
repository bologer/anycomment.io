<?php

namespace AnyComment\Cron;

use AnyComment\AnyCommentCore;
use AnyComment\AnyCommentServiceApi;
use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\AnyCommentUserMeta;
use AnyComment\Api\AnyCommentServiceSyncIn;
use AnyComment\Base\BaseObject;
use AnyComment\Rest\AnyCommentSocialAuth;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AnyCommentServiceSyncCron extends BaseObject {
	/**
	 * Init class.
	 */
	public function init() {
		if ( AnyCommentIntegrationSettings::is_sass_comments_sync() ) {
			$token = AnyCommentServiceApi::getSyncApiKey();

			if ( ! empty( $token ) ) {
				add_filter( 'cron_schedules', [ $this, 'add_minute_interval' ] );

				if ( ! wp_next_scheduled( 'anycomment_service_sync_cron' ) ) {
					wp_schedule_event( time(), 'every_minute', 'anycomment_service_sync_cron' );
				}

				add_action( 'anycomment_service_sync_cron', [ $this, 'sync_comments' ] );
			}
		}
	}

	/**
	 * Add new every minute interval.
	 *
	 * @param array $schedules List of available schedules.
	 *
	 * @return mixed
	 */
	public function add_minute_interval( $schedules ) {
		$schedules['every_minute'] = array(
			'interval' => 60,
			'display'  => esc_html__( 'Every Minute' ),
		);

		return $schedules;
	}

	/**
	 * Processing two-syncing syncing of comments.
	 *
	 * @return bool
	 */
	public function sync_comments() {
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
	public function sync_comments_in() {
		return ( new AnyCommentServiceSyncIn() )->sync();
	}

	/**
	 * Send comments to the service.
	 *
	 * @return bool
	 */
	public function sync_comments_out() {
		$comment_id = static::getSyncCommentId();

		$log = AnyCommentCore::logger();

		$log->info( 'Starting to sync comments with service from comment ID ' . $comment_id );

		if ( empty( $comment_id ) ) {
			$comment_id = 0;

			$log->info( 'Reset comment ID to 0 as it was empty...' );
		}

		global $wpdb;

		// Select only those which are not exported to the service yet
		$meta_key = AnyCommentServiceSyncIn::getCommentImportedMetaKey();
		$sql      = <<<SQL
SELECT c.* FROM {$wpdb->comments} AS c 
WHERE NOT EXISTS (
	SELECT * 
	FROM {$wpdb->commentmeta} cm
	WHERE c.comment_ID = cm.comment_id AND cm.meta_key = %s
) AND c.comment_id > %d AND c.comment_type IN ('', 'comment', 'review') LIMIT 10
SQL;

		$prepare = $wpdb->prepare( $sql, $meta_key, $comment_id );

		$comments = $wpdb->get_results( $prepare );

		if ( empty( $comments ) ) {
			AnyCommentCore::logger()->info( 'No comments to sync with service, skipping...' );

			return false;
		}

		$log->info( 'Have ' . count( $comments ) . ' to sync with service, ready to process' );

		foreach ( $comments as $key => $comment ) {
			$comment = new \WP_Comment( $comment );
			$this->sync_comment( $comment );
		}

		return true;
	}

	/**
	 * @param \WP_Comment $comment
	 *
	 * @return bool
	 */
	public function sync_comment( \WP_Comment $comment ) {
		$log = AnyCommentCore::logger();

		try {
			$log->info( 'Ready to sync comment #' . $comment->comment_ID );

			$post = get_post( $comment->comment_post_ID );

			if ( empty( $post ) ) {
				$log->error( 'Comment #' . $comment->comment_ID . ' does not have post, skipping it' );
				// Skip this comment as we cannot save it without post URL
				static::update_sync_comment_id( $comment->comment_ID );

				return false;
			}

			$page_url = get_permalink( $post );

			if ( $page_url === false ) {
				$log->error( 'Unable to get page url for comment #' . $comment->comment_ID . ', skipping it' );
				// Skip this comment as we have to have URL in order to display comments
				static::update_sync_comment_id( $comment->comment_ID, true );

				return false;
			}

			$post_thumbnail_url = get_the_post_thumbnail_url( $post );
			$page_preview_url   = $post_thumbnail_url !== false ? $post_thumbnail_url : null;
			$page_author        = get_the_author_meta( 'user_nicename', $post->post_author );


			if ( ! empty( $comment->user_id ) ) {
				$user = get_userdata( $comment->user_id );

				if ( $user === false ) {
					$log->error( 'Comment #' . $comment->comment_ID . ' has user ID, but not able to retrieve its data, skipping it' );
					static::update_sync_comment_id( $comment->comment_ID, true );

					return false;
				}

				$profileUrl = null;

				if ( ! empty( $user->user_url ) ) {
					$profileUrl = $user->user_url;
				} elseif ( ( $socialUrl = AnyCommentUserMeta::get_social_profile_url( $user->ID ) ) !== null ) {
					$profileUrl = $socialUrl;
				} elseif ( ! empty( $comment->comment_author_url ) ) {
					$profileUrl = $comment->comment_author_url;
				}

				$author = [
					'name'     => $user->display_name,
					'username' => $user->user_login,
					'email'    => $user->user_email,
					'avatar'   => AnyCommentSocialAuth::get_user_avatar_url( $user->ID ),
					'url'      => $profileUrl
				];
			} else {
				$profileUrl = null;

				if ( ! empty( $comment->comment_author_url ) ) {
					$profileUrl = $comment->comment_author_url;
				}

				$author = [
					'name'   => $comment->comment_author,
					'email'  => ! empty( $comment->comment_author_email ) ?
						$comment->comment_author_email :
						null,
					'avatar' => ! empty( $comment->comment_author_email ) ?
						get_avatar_url( $comment->comment_author_email ) :
						null,
					'url'    => $profileUrl
				];
			}

			$body = [
				'page_url'         => $page_url,
				'page_title'       => $post->post_title,
				'page_preview_url' => $page_preview_url,
				'page_author'      => $page_author,
				'comment'          => [
					'id'           => $comment->comment_ID,
					'parent_id'    => $comment->comment_parent > 0 ? $comment->comment_parent : null,
					'status'       => $this->getServiceStatus( $comment ),
					'content'      => $comment->comment_content,
					'ip'           => $comment->comment_author_IP,
					'created_date' => $comment->comment_date
				],
				'author'           => $author
			];

			$resp = AnyCommentServiceApi::request()->post(
				'client/comment/add',
				$body,
				[ 'token' => AnyCommentServiceApi::getSyncApiKey() ]
			);

			if ( is_wp_error( $resp ) || ! isset( $resp['response']['code'] ) ) {
				$log->error( 'Failed to sync comment #' . $comment->comment_ID . ' with service, error returned' );
				static::update_sync_comment_id( $comment->comment_ID, true );

				return false;
			}

			$response_code = (int) $resp['response']['code'];

			if ( $response_code === 200 ) {
				$jsonString = wp_remote_retrieve_body( $resp );
				$data       = json_decode( $jsonString, true );

				if ( isset( $data['response']['id'] ) && is_int( $data['response']['id'] ) ) {
					static::update_sync_comment_id( $comment->comment_ID );

					return true;
				}
			}

			$log->error( 'Comment #' . $comment->comment_ID . ' failed to sync, ' . $response_code . ' HTTP code was returned, marking as failed' );

			static::update_sync_comment_id( $comment->comment_ID, true );

		} catch ( \Exception $exception ) {
			$log->error(
				'Failed to sync comment #' . $comment->comment_ID . ' as exception happened: ' . $exception->getMessage() . ', trace: ' . $exception->getTraceAsString()
			);
		} catch ( \Throwable $exception ) {
			$log->error(
				'Failed to sync comment #' . $comment->comment_ID . ' as exception happened: ' . $exception->getMessage() . ', trace: ' . $exception->getTraceAsString()
			);
		}

		return false;
	}

	/**
	 * Updates comment's id in the database.
	 *
	 * @param int $comment_id
	 * @param bool $mark_failed Mark comment as failed in meta.
	 *
	 * @return bool
	 */
	public static function update_sync_comment_id( $comment_id, $mark_failed = false ) {
		if ( $mark_failed ) {
			update_comment_meta( $comment_id, self::getSyncFailedOptionName(), 1 );
		}

		return update_option( self::getSyncCommentIdOptionName(), $comment_id );
	}


	/**
	 * Get last sync comment id.
	 *
	 * @param null $default
	 *
	 * @return mixed|void
	 */
	public static function getSyncCommentId( $default = null ) {
		return get_option( static::getSyncCommentIdOptionName(), $default );
	}

	/**
	 * Converts WordPress's status to service's one.
	 *
	 * @param \WP_Comment $comment
	 *
	 * @return int
	 */
	public function getServiceStatus( \WP_Comment $comment ) {
		switch ( $comment->comment_approved ) {
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
	public static function getSyncCommentIdOptionName() {
		return 'anycomment_last_sync_id';
	}

	/**
	 * @return string
	 */
	public static function getSyncFailedOptionName() {
		return 'anycomment_sync_failed';
	}

	/**
	 * Returns sync information.
	 *
	 * @return array
	 */
	public static function getSyncInfo() {
		global $wpdb;

		$comment_id = intval( static::getSyncCommentId( 0 ) );

		$info = [];

		$meta_key     = AnyCommentServiceSyncIn::getCommentImportedMetaKey();
		$global_total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} AS c 
WHERE NOT EXISTS (
	SELECT * 
	FROM {$wpdb->commentmeta} cm
	WHERE c.comment_ID = cm.comment_id AND cm.meta_key = %s
)", $meta_key ) );

		$info['total'] = $global_total;

		if ( empty( $comment_id ) ) {
			$info['complete_percent'] = 0;
			$info['current']          = 0;
			$info['remaining']        = $global_total;
		} else {
			$remaining = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->comments} AS c 
WHERE NOT EXISTS (
	SELECT * 
	FROM {$wpdb->commentmeta} cm
	WHERE c.comment_ID = cm.comment_id AND cm.meta_key = %s
) AND c.comment_id > %d AND c.comment_type IN ('', 'comment') LIMIT 10",
				$meta_key,
				$comment_id,
				$comment_id
			) );

			$remaining = intval( $remaining );

			$info['complete_percent'] = $remaining > 0 ?
				round( ( 1 - ($remaining / $global_total) ) * 100, 2 ) :
				100;
			$info['current']          = $global_total - $remaining;
			$info['remaining']        = $remaining;
		}

		return $info;
	}
}
