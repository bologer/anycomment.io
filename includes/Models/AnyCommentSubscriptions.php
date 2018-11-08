<?php
namespace AnyComment\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Helpers\AnyCommentRequest;

/**
 * Class AnyCommentSubscriptions keeps information about current comment
 * subscriptions.
 *
 * @property int $ID Primary key.
 * @property int $post_ID Post ID for which user subscribed.
 * @property int|null $user_ID User ID who subscribed (when not guest).
 * @property string $email Email of the user who subscribes.
 * @property bool $is_active Whether subscription is active or not.
 * @property string $user_agent
 * @property string|null $ip User IP who subscribes.
 * @property string $token Used to confirm actions.
 * @property int|null $confirmed_at When subscription was confirmed.
 * @property int $created_at UNIX timestamp when entry was created.
 *
 * @since 0.0.68
 */
class AnyCommentSubscriptions {

	public $ID;
	public $post_ID;
	public $user_ID;
	public $email;
	public $is_active;
	public $user_agent;
	public $ip;
	public $token;
	public $confirmed_at;
	public $created_at;

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function tableName() {
		global $wpdb;

		return $wpdb->prefix . 'anycomment_subscriptions';
	}


	/**
	 * Notify subscribers by specified comment.
	 * Expected that specified comment is the new comment.
	 *
	 * @param mixed $comment Comment ID or instance of WP_Comment
	 *
	 * @see WP_Comment for further information.
	 *
	 * @return bool
	 */
	public static function notify_by( $comment ) {

		if ( ! AnyCommentGenericSettings::is_notify_subscribers() ) {
			return false;
		}

		$working_comment = get_comment( $comment );

		if ( (int) $working_comment->comment_post_ID === 0 ) {
			return false;
		}

		global $wpdb;

		$table = static::tableName();
		$sql   = "SELECT `user_ID`, `email` FROM $table WHERE post_ID=%d AND is_active=1 AND confirmed_at IS NOT NULL";

		/**
		 * @var AnyCommentSubscriptions[]|null $subscribers
		 */
		$subscribers = $wpdb->get_results( $wpdb->prepare( $sql, [ $working_comment->comment_post_ID ] ) );

		if ( empty( $subscribers ) ) {
			return true;
		}

		foreach ( $subscribers as $key => $subscriber ) {
			AnyCommentEmailQueue::add_as_subscriber_notification( $subscriber->email, $comment );
		}

		return true;
	}

	/**
	 * Check whether user subscribed by specified field and search value.
	 *
	 * @param string $email Database field to search for. Should be taken from table schema.
	 * @param int|null $post_id Post ID to check whether user is subsribed to it. Leave empty to check just for email.
	 *
	 * @return bool
	 */
	public static function is_subscribed_by( $email, $post_id = null ) {

		$condition_array = [ $email ];
		$condition       = "email = %s";

		if ( $post_id !== null ) {
			$condition         .= ' AND post_ID = %d';
			$condition_array[] = $post_id;
		}

		global $wpdb;


		$tableName = static::tableName();

		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM $tableName WHERE $condition", $condition_array );
		$count = $wpdb->get_var( $sql );

		return $count >= 1;
	}

	/**
	 * Inserts new model.
	 *
	 *
	 * @return false|$this
	 */
	public function save() {

		if ( ! isset( $this->user_agent ) ) {
			$this->user_agent = AnyCommentRequest::get_user_agent();
		}

		if ( ! isset( $this->ip ) ) {
			$this->ip = AnyCommentRequest::get_user_ip();
		}

		if ( ! isset( $this->created_at ) ) {
			$this->created_at = time();
		}

		global $wpdb;

		unset( $this->ID );

		$count = $wpdb->insert( static::tableName(), (array) $this );

		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$this->ID = $lastId;

			return true;
		}

		return false;
	}
}