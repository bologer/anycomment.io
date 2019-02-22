<?php

namespace AnyComment\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_Comment;

use AnyComment\Helpers\AnyCommentRequest;

/**
 * Class AnyCommentLikes is a database model to manage likes.
 *
 * @property int $ID
 * @property int|null $user_ID
 * @property int $comment_ID
 * @property int $post_ID
 * @property string $user_agent
 * @property string $ip
 * @property string $liked_at
 * @property int $type Type - like/dislike.
 *
 * @since 0.0.3
 */
class AnyCommentLikes extends AnyCommentActiveRecord {

	/**
	 * Like type for `type` column.
	 */
	const TYPE_LIKE = 1;

	/**
	 * Dislike type for `type` column.
	 */
	const TYPE_DISLIKE = 0;

	/**
	 * {@inheritdoc}
	 */
	public static $table_name = 'likes';

	public $ID;
	public $user_ID;
	public $comment_ID;
	public $post_ID;
	public $user_agent;
	public $ip;
	public $liked_at;
	public $type;

	/**
	 * Get comment summary of likes/dislikes.
	 *
	 * @param int $comment_id Comment id to check summary for.
	 *
	 * @return object Object with `likes` and `dislikes` as properties.
	 */
	public static function get_summary( $comment_id ) {
		global $wpdb;

		$table_name = static::get_table_name();

		$sql = "SELECT COUNT(l.ID) AS likes, COUNT(d.ID) AS dislikes 
FROM `$table_name` initial_likes
LEFT JOIN `$table_name` l ON l.ID = initial_likes.ID AND l.type = %d
LEFT JOIN `$table_name` d ON d.ID = initial_likes.ID AND d.type = %d
WHERE initial_likes.comment_ID = %d";

		$res = $wpdb->get_row( $wpdb->prepare( $sql, [ self::TYPE_LIKE, self::TYPE_DISLIKE, $comment_id ] ) );

		if ( empty( $res ) ) {
			$obj           = new \stdClass();
			$obj->likes    = 0;
			$obj->dislikes = 0;
			$obj->rating   = 0;

			return $obj;
		}

		if ( isset( $res->likes ) ) {
			$res->likes = (int) $res->likes;
		}

		if ( isset( $res->dislikes ) ) {
			$res->dislikes = (int) $res->dislikes;
		}

		$res->rating = static::count_rating( $res->likes, $res->dislikes );

		return $res;
	}

	/**
	 * Gets proper rating based on likes and dislikes.
	 *
	 *
	 *
	 * @param int $comment_id Comment id to get rating for.
	 *
	 * @return int
	 */
	public static function get_rating( $comment_id ) {
		$summary = static::get_summary( $comment_id );

		$likes    = $summary->likes;
		$dislikes = $summary->dislikes;

		static::count_rating( $likes, $dislikes );
	}

	/**
	 * Count rating and dislikes together.
	 *
	 * Formula: likes - dislikes = rating
	 *
	 * @param int $likes Number of Likes.
	 * @param int $dislikes Number of dislikes.
	 *
	 * @return int
	 */
	public static function count_rating( $likes, $dislikes ) {
		if ( $likes === 0 && $dislikes === 0 ) {
			return 0;
		}

		return $likes - $dislikes;
	}

	/**
	 * Check whether current user or specified one has like/dislike
	 * on the comment or not.
	 *
	 * @param int $type Type: like or dislike. Use constants TYPE_*.
	 * @param int $comment_id Comment id.
	 * @param int|null $user_id User id. When user id is not provided,
	 *                          active session would be used to retrieve it
	 *                          if user is guest, current user IP address would instead.
	 *
	 * @return bool
	 */
	public static function is_user_has( $type, $comment_id, $user_id = null ) {

		if ( $type !== self::TYPE_LIKE && $type !== self::TYPE_DISLIKE ) {
			return false;
		}

		if ( ! ( $comment = get_comment( $comment_id ) ) instanceof WP_Comment ) {
			return false;
		}

		global $wpdb;

		$table_name = static::get_table_name();

		$sql = "SELECT COUNT(*) FROM $table_name WHERE";

		if ( is_numeric( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );

			if ( false === $user ) {
				return false;
			}

			$sql .= $wpdb->prepare( " user_ID = %d", $user->ID );
		} else {
			$user_id = (int) get_current_user_id();

			if ( $user_id !== 0 ) {
				$sql .= $wpdb->prepare( " user_ID = %d", $user_id );
			} else {
				$sql .= $wpdb->prepare( " user_ID IS NULL AND ip = %s", AnyCommentRequest::get_user_ip() );
			}
		}

		$sql   .= $wpdb->prepare( " AND comment_ID = %s AND type = %d", $comment->comment_ID, $type );
		$count = $wpdb->get_var( $sql );

		return $count >= 1;
	}

	/**
	 * Check whether current or specified user has like or not.
	 *
	 * @param int $comment_id Comment id.
	 *
	 * @param int|null $user_id User id.
	 *
	 * @return bool|int
	 */
	public static function is_user_has_like( $comment_id, $user_id = null ) {
		return static::is_user_has( self::TYPE_LIKE, $comment_id, $user_id );
	}

	/**
	 * Check whether current or specified user has dislike or not.
	 *
	 * @param int $comment_id Comment id.
	 *
	 * @param int|null $user_id User id.
	 *
	 * @return bool|int
	 */
	public static function is_user_has_dislike( $comment_id, $user_id = null ) {
		return static::is_user_has( self::TYPE_DISLIKE, $comment_id, $user_id );
	}

	/**
	 * Get likes count per comment.
	 *
	 * @param int $userId User ID to search for.
	 *
	 * @return int
	 */
	public static function get_likes_count_by_user( $userId ) {
		global $wpdb;

		$table_name = static::get_table_name();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `user_ID`=%d AND `type` = %d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $userId, self::TYPE_LIKE ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count;
	}


	/**
	 * Get likes count per comment.
	 *
	 * @param int $commentId Comment ID to be searched for.
	 *
	 * @return int
	 */
	public static function get_likes_count( $commentId ) {
		global $wpdb;

		$table_name = static::get_table_name();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `comment_ID`=%d AND `type` = %d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $commentId, self::TYPE_LIKE ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count;
	}

	/**
	 * Delete all like by provided comment ID.
	 *
	 * @since 0.0.3
	 *
	 * @param int $commentId ID of the comment to delete like from.
	 *
	 * @return bool
	 */
	public static function deleteLikes( $commentId ) {
		if ( empty( $commentId ) ) {
			return false;
		}

		$userId = get_current_user_id();

		if ( (int) $userId === 0 ) {
			return false;
		}

		global $wpdb;

		$rows = $wpdb->delete( static::get_table_name(), [ 'comment_ID' => $commentId, 'type' => self::TYPE_LIKE ] );

		return $rows !== false && $rows >= 0;
	}

	/**
	 * Delete item by type.
	 *
	 * @param int $type Like or dislike. You constants for it.
	 * @param int $comment_id Comment id.
	 * @param int|null $user_id User id. When user id is not provided,
	 *                          active session would be used to retrieve it
	 *                          if user is guest, current user IP address would instead.
	 *
	 * @return bool
	 */
	public static function delete_by_type( $type, $comment_id, $user_id = null ) {
		if ( $type !== self::TYPE_LIKE && $type !== self::TYPE_DISLIKE ) {
			return false;
		}

		if ( ! ( $comment = get_comment( $comment_id ) ) instanceof WP_Comment ) {
			return false;
		}

		$where = [];

		if ( is_numeric( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );

			if ( false === $user ) {
				return false;
			}

			$where['user_ID'] = $user->ID;
		} else {
			$user_id = (int) get_current_user_id();

			if ( $user_id !== 0 ) {
				$where['user_ID'] = $user_id;
			} else {
				$where['user_ID'] = null;
				$where['ip']      = AnyCommentRequest::get_user_ip();
			}
		}

		$where['comment_ID'] = $comment->comment_ID;
		$where['type']       = $type;

		global $wpdb;

		$rows = $wpdb->delete( static::get_table_name(), $where );

		return $rows !== false && $rows >= 0;
	}


	/**
	 * Delete single like.
	 *
	 * @since 0.0.3
	 *
	 * @param int $comment_id ID of the comment to delete like from.
	 * @param int|null $user_id User id or NULL then current logged in user id would be taken.
	 *
	 * @return bool
	 */
	public static function delete_like( $comment_id, $user_id = null ) {
		return static::delete_by_type( self::TYPE_LIKE, $comment_id, $user_id );
	}

	/**
	 * Delete single dislike.
	 *
	 * @since 0.0.
	 *
	 * @param int $comment_id ID of the comment to delete like from.
	 * @param int|null $user_id User id or NULL then current logged in user id would be taken.
	 *
	 * @return bool
	 */
	public static function delete_dislike( $comment_id, $user_id = null ) {
		return static::delete_by_type( self::TYPE_DISLIKE, $comment_id, $user_id );
	}

	/**
	 * Inserts a comment into the database.
	 *
	 * @since 0.0.3
	 *
	 * @return bool
	 */
	public function save() {
		if ( ! isset( $this->ip ) ) {
			$this->ip = AnyCommentRequest::get_user_ip();
		}

		if ( ! isset( $this->liked_at ) ) {
			$this->liked_at = current_time( 'mysql' );
		}

		if ( isset( $this->user_ID ) && (int) $this->user_ID === 0 ) {
			$this->user_ID = null;
		}

		global $wpdb;

		$tableName = static::get_table_name();

		unset( $this->ID );

		$count = $wpdb->insert( $tableName, (array) $this );


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
