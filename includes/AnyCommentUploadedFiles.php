<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentLikes.
 *
 * @property int $ID
 * @property int $post_ID
 * @property int|null $user_ID
 * @property string|null $url
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int $created_at
 *
 * @since 0.0.3
 */
class AnyCommentUploadedFiles {

	public $ID;
	public $post_ID;
	public $user_ID;
	public $url;
	public $ip;
	public $user_agent;
	public $created_at;

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function tableName() {
		return 'anycomment_uploaded_files';
	}

	/**
	 * @param $commentId
	 *
	 * @return bool|int
	 */
	public static function isCurrentUserHasLike( $commentId, $userId = null ) {
		if ( ! ( $comment = get_comment( $commentId ) ) instanceof WP_Comment ) {
			return false;
		}

		if ( $userId === null && ! (int) ( $userId = get_current_user_id() ) === 0 ) {
			return false;
		}

		if ( $userId !== null && ! get_user_by( 'id', $userId ) ) {
			return false;
		}

		global $wpdb;


		$tableName = static::tableName();

		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM $tableName WHERE `user_ID` =%d AND `comment_ID`=%s", $userId, $comment->comment_ID );
		$count = $wpdb->get_var( $sql );

		return $count >= 1;
	}

	/**
	 * Get likes count per comment.
	 *
	 * @param string $ip User IP address to check for.
	 *
	 * @return int
	 */
	public static function isOverLimitByIp( $ip = null ) {
		global $wpdb;

		if ( $ip === null ) {
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		$minutes     = 10;
		$intervalTime = strtotime( "-{$minutes} minutes" );
		$limit        = 2;

		$table_name = static::tableName();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `ip`=%s AND `created_at` >= %d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $ip, $intervalTime ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count >= $limit;
	}

	/**
	 * Inserts uploaded file to track limits, etc.
	 *
	 * @since 0.0.52
	 *
	 * @return false|AnyCommentLikes|object
	 */
	public function save() {
		if ( ! isset( $this->ip ) ) {
			$this->ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
		}

		if ( ! isset( $this->user_agent ) ) {
			$this->user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
		}

		if ( ! isset( $this->created_at ) ) {
			$this->created_at = time();
		}

		global $wpdb;

		$tableName = static::tableName();

		unset( $this->ID );

		$count = $wpdb->insert( $tableName, (array) $this );


		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$this->ID = $lastId;

			return $this;
		}

		return false;
	}
}