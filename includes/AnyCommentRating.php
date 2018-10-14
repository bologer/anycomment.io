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
 * @property int $rating
 * @property string $user_agent
 * @property string $ip
 * @property string $created_at
 *
 * @since 0.0.61
 */
class AnyCommentRating {

	public $ID;
	public $post_ID;
	public $user_ID;
	public $rating;
	public $user_agent;
	public $ip;
	public $created_at;

	/**
	 * Get table name.
	 *
	 * @since 0.0.61
	 *
	 * @return string
	 */
	public static function tableName() {
		global $wpdb;

		return $wpdb->prefix . 'anycomment_rating';
	}

	/**
	 * Get count of total votes by specified post ID.
	 *
	 * @param int $post_id Post ID to count for.
	 *
	 * @since 0.0.61
	 *
	 * @return int
	 */
	public static function get_count_by_post( $post_id ) {
		global $wpdb;

		$table = static::tableName();

		$sql = "SELECT COUNT(*) FROM `$table` WHERE `post_ID`=%d";

		$res = $wpdb->get_var( $wpdb->prepare( $sql, [ $post_id ] ) );

		if ( $res === null ) {
			return 0;
		}

		return (int) $res;
	}

	/**
	 * Get average rating value by sepcified post ID.
	 *
	 * @param int $post_id Post ID to calculate average for.
	 *
	 * @since 0.0.61
	 *
	 * @return int
	 */
	public static function get_average_by_post( $post_id ) {
		global $wpdb;

		$table = static::tableName();

		$sql = "SELECT ROUND(AVG(rating), 1) FROM `$table` WHERE `post_ID`=%d";
		$res = $wpdb->get_var( $wpdb->prepare( $sql, [ $post_id ] ) );

		if ( $res === null ) {
			return 0;
		}

		return $res;
	}


	/**
	 * Check whether passed user id rated page or not.
	 *
	 * @param $post_id
	 * @param $user_id
	 *
	 * @since 0.0.61
	 *
	 * @return bool|int
	 */
	public static function current_user_rated( $post_id, $user_id ) {

		$post = get_post( $post_id );
		$user = get_user_by( 'id', $user_id );

		if ( $post === null || $user === false ) {
			return false;
		}

		$table = static::tableName();

		global $wpdb;
		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM `$table` WHERE `post_ID` =%d AND `user_ID`=%d", $post_id, $user_id );
		$count = $wpdb->get_var( $sql );

		return $count >= 1;
	}

	/**
	 * Inserts a comment into the database.
	 *
	 * @since 0.0.61
	 *
	 * @param int $rating Rating between 1-5.
	 * @param int $post_id Post ID for which rate is being added.
	 * @param int $user_id User ID who is rating.
	 *
	 * @return false|AnyCommentRating
	 */
	public static function add_rating( $rating, $post_id, $user_id ) {

		$rating = (int) $rating;

		if ( $rating < 1 || $rating > 5 ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( $post === null ) {
			return false;
		}

		$user = get_user_by( 'id', $user_id );

		if ( ! $user ) {
			return false;
		}

		$model             = new self();
		$model->post_ID    = $post->ID;
		$model->user_ID    = $user->ID;
		$model->rating     = $rating;
		$model->user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$model->ip         = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
		$model->created_at = time();

		global $wpdb;

		$tableName = static::tableName();

		unset( $model->ID );

		$count = $wpdb->insert( $tableName, (array) $model );


		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$model->ID = $lastId;

			return $model;
		}

		return false;
	}
}