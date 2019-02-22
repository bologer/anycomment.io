<?php

namespace AnyComment\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Helpers\AnyCommentRequest;

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
class AnyCommentRating extends AnyCommentActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static $table_name = 'rating';

	public $ID;
	public $post_ID;
	public $user_ID;
	public $rating;
	public $user_agent;
	public $ip;
	public $created_at;

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

		$table = static::get_table_name();

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

		$table = static::get_table_name();

		$sql = "SELECT ROUND(AVG(rating), 1) FROM `$table` WHERE `post_ID`=%d";
		$res = $wpdb->get_var( $wpdb->prepare( $sql, [ $post_id ] ) );

		if ( $res === null ) {
			return 0;
		}

		return $res;
	}


	/**
	 * Check whether passed user id or IP address has rated.
	 *
	 * @param int $post_id Post ID.
	 * @param int|string|null $user_id_or_ip User ID or IP address. When NULL currnet user's IP address will be used.
	 *
	 * @since 0.0.61
	 *
	 * @return bool|int
	 */
	public static function current_user_rated( $post_id, $user_id_or_ip ) {

		$post = get_post( $post_id );

		if ( $post === null ) {
			return false;
		}

		if ( is_numeric( $user_id_or_ip ) && (int) $user_id_or_ip !== 0 ) {
			$user = get_user_by( 'id', $user_id_or_ip );

			if ( ! $user ) {
				return false;
			}

			$field      = 'user_ID';
			$field_type = '%d';
		} else {
			$field_type = '%s';
			$field      = 'ip';
		}

		$table = static::get_table_name();

		global $wpdb;
		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM `$table` WHERE `post_ID` =%d AND `$field`=$field_type", $post_id, $user_id_or_ip );
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
	 * @param int|string|null $user_id_or_ip User ID or IP address who is placing rating. When NULL current users IP address will be used.
	 *
	 * @return false|AnyCommentRating
	 */
	public static function add_rating( $rating, $post_id, $user_id_or_ip = null ) {

		$rating = (int) $rating;

		if ( $rating < 1 || $rating > 5 ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( $post === null ) {
			return false;
		}

		$model = new self();

		$user_ip = AnyCommentRequest::get_user_ip();

		if ( is_numeric( $user_id_or_ip ) ) {
			$user = get_user_by( 'id', $user_id_or_ip );

			if ( ! $user ) {
				return false;
			}

			$model->user_ID = $user->ID;
			$model->ip      = $user_ip;
		} else {
			$model->ip = $user_ip;
		}

		$model->post_ID = $post->ID;

		$model->rating     = $rating;
		$model->user_agent = AnyCommentRequest::get_user_agent();
		$model->created_at = time();

		global $wpdb;

		$tableName = static::get_table_name();

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
