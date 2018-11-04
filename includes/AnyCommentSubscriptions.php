<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
	 * Check whether user subscribed by specified field and search value.
	 *
	 * @param string $field Database field to search for. Should be taken from table schema.
	 * @param string $search Value to search for.
	 *
	 * @return bool
	 */
	public static function is_subscribed_by( $field = 'email', $search ) {

		if ( $field !== 'email' && $field !== 'id' ) {
			$field = 'email';
		}

		global $wpdb;


		$tableName = static::tableName();

		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM $tableName WHERE `$field`=%s", $search );
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
			$this->user_agent = AnyCommentHelper::get_user_agent();
		}

		if ( ! isset( $this->ip ) ) {
			$this->ip = AnyCommentHelper::get_user_ip();
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