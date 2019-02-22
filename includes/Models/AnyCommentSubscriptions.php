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
class AnyCommentSubscriptions extends AnyCommentActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static $table_name = 'subscriptions';

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
	 * Find subscriber by token to confirm subscription.
	 *
	 * @param string $token
	 *
	 * @return $this|null
	 */
	public static function find_by_token ( $token ) {

		$token = trim( $token );

		if ( empty( $token ) ) {
			return null;
		}

		global $wpdb;
		$table        = static::get_table_name();
		$prepared_sql = $wpdb->prepare( "SELECT * FROM {$table} WHERE token = %s", [ $token ] );

		$row = $wpdb->get_row( $prepared_sql );

		return empty( $row ) ? null : $row;
	}

	/**
	 * Find entry by email and post id.
	 *
	 * @param string $email Email.
	 * @param int $post_id Post ID.
	 *
	 * @return $this|null
	 */
	public static function find_by_email_post ( $email, $post_id ) {

		if ( empty( $email ) || empty( $post_id ) ) {
			return null;
		}

		global $wpdb;
		$table        = static::get_table_name();
		$prepared_sql = $wpdb->prepare( "SELECT * FROM $table WHERE email = %s AND post_ID = %d", [
			$email,
			$post_id,
		] );

		$row = $wpdb->get_row( $prepared_sql );

		return empty( $row ) ? null : $row;
	}

	/**
	 * Method allows to update token by provided where condition.
	 *
	 * @param array $where Key => list conditions.
	 *
	 * @return false|int
	 */
	public static function refresh_token_by ( $where ) {
		global $wpdb;

		if ( isset( $where['id'] ) ) {
			$where['ID'] = $where['id'];
			unset( $where['id'] );
		}

		$new_token = static::generate_token();

		$affected_rows = $wpdb->update( static::get_table_name(), [ 'token' => $new_token ], $where );

		if ( $affected_rows > 0 ) {
			return $new_token;
		}

		return null;
	}

	/**
	 * Mark subscriber as active by provided token.
	 *
	 * Notice: method would unset token automatically.
	 *
	 * @param string $token Value of the field to search for.
	 * @param bool $unset_token If required to unset token as well.
	 *
	 * @return false|int false on failure, int when some rows affected (success).
	 */
	public static function mark_as_active_by_token ( $token, $unset_token = true ) {
		global $wpdb;
		$table = static::get_table_name();

		$data = [ 'is_active' => 1, 'confirmed_at' => time() ];

		if ( $unset_token ) {
			$data['token'] = null;
		}

		return $wpdb->update( $table, $data, [ 'token' => $token, 'is_active' => 0 ] );
	}

	/**
	 * Mark subscriber as inactive by provided token.
	 *
	 * Notice: method would unset token automatically.
	 *
	 * @param string $token Value of the field to search for.
	 * @param bool $unset_token If required to unset token as well.
	 *
	 * @return false|int false on failure, int when some rows affected (success).
	 */
	public static function mark_as_inactive_by_token ( $token, $unset_token = true ) {
		global $wpdb;
		$table = static::get_table_name();

		$data = [ 'is_active' => 0 ];

		if ( $unset_token ) {
			$data['token'] = null;
		}

		return $wpdb->update( $table, $data, [ 'token' => $token, 'is_active' => 1 ] );
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
	public static function notify_by ( $comment ) {

		if ( ! AnyCommentGenericSettings::is_notify_subscribers() ) {
			return false;
		}

		$working_comment = get_comment( $comment );

		if ( empty( $working_comment ) || (int) $working_comment->comment_ID === 0 ) {
			return false;
		}

		global $wpdb;

		$table = static::get_table_name();
		$sql   = "SELECT * FROM $table WHERE post_ID = %d AND is_active = 1 AND confirmed_at IS NOT NULL";

		/**
		 * @var AnyCommentSubscriptions[]|null $subscribers
		 */
		$subscribers = $wpdb->get_results( $wpdb->prepare( $sql, [ $working_comment->comment_post_ID ] ) );

		if ( empty( $subscribers ) ) {
			return true;
		}

		/**
		 * @var AnyCommentSubscriptions $subscription
		 */
		foreach ( $subscribers as $key => $subscription ) {
			AnyCommentEmailQueue::add_as_subscriber_notification( $subscription, $comment );
		}

		return true;
	}

	/**
	 * Delete subscription by ID.
	 *
	 * @param int $id Id of the subscription to delete.
	 *
	 * @return false|int
	 */
	public function delete_by_id ( $id ) {
		global $wpdb;

		return $wpdb->delete( static::get_table_name(), [ 'ID' => $id ] );
	}

	/**
	 * Check whether user subscribed by specified field and search value.
	 *
	 * @param string $email Database field to search for. Should be taken from table schema.
	 * @param mixed $post Post ID or instance to check whether user is subscribed to it. Leave empty to check just for email.
	 *
	 * @return bool
	 */
	public static function is_subscribed_by ( $email, $post = null ) {

		$condition_array = [ $email ];
		$condition       = "email = %s";

		if ( $post !== null ) {

			$post_id = null;

			if ( $post instanceof \WP_Post ) {
				$post_id = $post->ID;
			} elseif ( is_numeric( $post ) ) {
				$post_id = $post;
			}

			if ( ! empty( $post_id ) ) {
				$condition         .= ' AND post_ID = %d';
				$condition_array[] = $post_id;
			}
		}

		global $wpdb;


		$tableName = static::get_table_name();

		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM $tableName WHERE $condition", $condition_array );
		$count = $wpdb->get_var( $sql );

		return $count >= 1;
	}

	/**
	 * Set token for the model.
	 */
	public function set_token () {
		$this->token = static::generate_token( 32 );
	}

	/**
	 * Generate token.
	 *
	 * @param int $length Length of the token.
	 *
	 * @return string
	 */
	public static function generate_token ( $length = 16 ) {
		return bin2hex( openssl_random_pseudo_bytes( $length ) );
	}

	/**
	 * Inserts new model.
	 *
	 *
	 * @return false|$this
	 */
	public function save () {

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

		$count = $wpdb->insert( static::get_table_name(), (array) $this );

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
