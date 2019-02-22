<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Models\AnyCommentUploadedFiles;

/**
 * Class AnyCommentCommentMeta is used to handle comment meta of comments.
 */
class AnyCommentCommentMeta {

	const META_ATTACHMENT = 'anycomment_attachment'; // Keeps list of comment attachments.

	const META_UPDATED_AT = 'anycomment_updated_at'; // Keeps UNIX timestamp when comment was updated.
	const META_UPDATED_BY = 'anycomment_updated_by'; // Keeps data about who updated the comment

	/**
	 * Delete attachments by meta id.
	 *
	 * @param int $meta_id Meta id.
	 *
	 * @return bool
	 */
	public static function delete_attachment( $meta_id ) {
		global $wpdb;

		$rowsAffected = $wpdb->delete( $wpdb->commentmeta, [ 'meta_id' => $meta_id ] );

		return $rowsAffected > 0;
	}

	/**
	 * Delete attachment by file id.
	 *
	 * @param $file_id
	 *
	 * @return bool
	 */
	public static function delete_attachment_by_file_id( $file_id ) {
		global $wpdb;

		$file_id = trim( $file_id );

		$rowsAffected = $wpdb->delete( $wpdb->commentmeta, [
			'meta_key'   => self::META_ATTACHMENT,
			'meta_value' => $file_id
		] );

		return $rowsAffected >= 0;
	}

	/**
	 * Check whether attachments exists by file id.
	 *
	 * @param int $file_id File ID to check for.
	 *
	 * @return bool
	 */
	public static function exist_by_attachment_file_id( $file_id ) {
		global $wpdb;

		$file_id = trim( $file_id );

		$sql = "SELECT * FROM `{$wpdb->commentmeta}` WHERE `meta_key` = %s AND `meta_value` = %d";

		$row = $wpdb->get_row( $wpdb->prepare( $sql, [ self::META_ATTACHMENT, $file_id ] ) );

		return ! empty( $row );
	}

	/**
	 * Get single attachment information.
	 *
	 * @param int $meta_id Meta ID.
	 * @param int $comment_id Comment ID.
	 * @param bool $withFile
	 *
	 * @return object|null
	 */
	public static function get_attachment( $meta_id, $comment_id = null, $withFile = true ) {
		global $wpdb;

		$sql = 'SELECT `meta`.*';

		if ( $withFile ) {
			$sql .= ', `files`.`ID` AS file_id, `files`.`post_ID` AS file_post_ID, `files`.`user_ID` AS file_user_ID, `files`.`type` AS file_type';
		}

		$sql .= " FROM `{$wpdb->commentmeta}` `meta`";

		if ( $withFile ) {
			$filesTable = AnyCommentUploadedFiles::get_table_name();
			$sql        .= " LEFT JOIN `$filesTable` `files` ON `meta`.`meta_value`=`files`.`id`";
		}

		$sql .= " WHERE `meta`.`meta_id` = %d";

		$preparedArray = [ $meta_id ];

		if ( $comment_id !== null ) {
			$sql             .= ' AND `meta`.`comment_id` = %d';
			$preparedArray[] = $comment_id;
		}

		$res = $wpdb->get_row( $wpdb->prepare( $sql, $preparedArray ) );

		if ( empty( $res ) ) {
			return null;
		}

		return $res;
	}

	/**
	 * Get attachments joined with files table.
	 *
	 * @param int $comment_id
	 *
	 * @return array|null|object
	 */
	public static function get_attachments( $comment_id ) {
		global $wpdb;

		$commentTable = $wpdb->commentmeta;
		$filesTable   = AnyCommentUploadedFiles::get_table_name();

		$sql = "SELECT 
`meta`.`meta_id` AS meta_id, 
`files`.`ID` AS file_id,
`files`.`type` AS file_type,
`files`.`url` AS file_url,
`files`.`url_thumbnail` AS file_thumbnail
FROM `$commentTable` `meta`
LEFT JOIN `$filesTable` `files` ON `meta`.`meta_value`=`files`.`id`
WHERE `meta`.`comment_id`=%d AND `meta`.`meta_key`=%s AND `meta`.`meta_value` IS NOT NULL AND `meta`.`meta_value` != '' AND `meta`.`meta_value` != '[]'";

		return $wpdb->get_results( $wpdb->prepare( $sql, [ $comment_id, self::META_ATTACHMENT ] ) );
	}

	/**
	 * Get attachments as plain string or array.
	 *
	 * @param int $comment_id Comment ID.
	 *
	 * @param bool $asObject If true, plain text will be returned, false will return array when value is not empty.
	 *
	 * @return string|array string when returned as plain text and array when returned as array.
	 */
	public static function get_attachments_for_api( $comment_id, $asObject = true ) {

		$res = static::get_attachments( $comment_id );

		$objectToReturn = [];

		if ( ! empty( $res ) ) {
			/**
			 * @var $meta
			 */
			foreach ( $res as $meta ) {
				$arr                   = [];
				$arr['file_type']      = AnyCommentUploadedFiles::get_image_type( $meta->file_type );
				$arr['file_url']       = $meta->file_url;
				$arr['file_thumbnail'] = $meta->file_thumbnail;
				$arr['file_id']        = $meta->file_id;
				$objectToReturn[]      = $arr;
			}
		}

		if ( $asObject ) {
			return $objectToReturn;
		}

		return json_encode( $objectToReturn );
	}

	/**
	 * Add attachment for a comment. Method is quite smart, it
	 * will skip attachments if they were previously added already.
	 *
	 * @param int $comment_id
	 * @param string|array $attachments List of attachments to process.
	 *
	 * @return bool
	 */
	public static function add_attachments( $comment_id, $attachments ) {
		if ( is_string( $attachments ) ) {
			$attachments = json_decode( $attachments, true );
		}

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$fileId = isset( $attachment['file_id'] ) ? $attachment['file_id'] : null;

				if ( $fileId === null ) {
					continue;
				}

				// When files wasn't added yet, should do so
				if ( ! static::exist_by_attachment_file_id( $fileId ) ) {
					add_comment_meta( $comment_id, self::META_ATTACHMENT, $fileId );
				}
			}
		}

		return true;
	}

	/**
	 * Mark comment as updated.
	 *
	 * @param mixed $comment Comment to be check. It could be ID or instance of WP_Comment.
	 * @param array|null $comment_data
	 *
	 * @return bool|int
	 */
	public static function mark_updated( $comment, $comment_data = null ) {
		$retrieved_comment = get_comment( $comment );

		if ( null === $retrieved_comment ) {
			return false;
		}

		$updated_by = '';

		if ( null !== $comment_data && isset( $comment_data['user_id'] ) && ! empty( $comment_data['user_id'] ) ) {
			$updated_by = $comment_data['user_id'];
		}

		$comment_metas = [ self::META_UPDATED_AT => time(), self::META_UPDATED_BY => $updated_by ];
		$count         = 0;

		foreach ( $comment_metas as $meta_key => $meta_value ) {
			$meta_updated = update_comment_meta( $retrieved_comment->comment_ID, $meta_key, $meta_value );

			if ( false !== $meta_updated ) {
				$count ++;
			}
		}

		return $count === count( $comment_metas );
	}

	/**
	 * Check by whome comment was update (if it was updated).
	 *
	 * @param mixed $comment Comment to be check. It could be ID or instance of WP_Comment.
	 * @param bool $user_instance Return user instance in cause user exists. User ID will be returned when false.
	 *
	 * @return int|null NULL returned in case when comment never updated, missing meta or user ID does not exist.
	 */
	public static function get_updated_by( $comment, $user_instance = false ) {
		$retrieved_comment = get_comment( $comment );

		if ( null === $retrieved_comment ) {
			return false;
		}

		$user_id = get_comment_meta( $retrieved_comment->comment_ID, self::META_UPDATED_BY, true );

		if ( empty( $user_id ) ) {
			return null;
		}

		$user = get_user_by( 'id', $user_id );

		if ( false === $user ) {
			// Unset comment meta value to empty as user does not exist
			update_comment_meta( $retrieved_comment->comment_ID, self::META_UPDATED_BY, '' );

			return null;
		}

		return $user_instance ? $user_instance : $user_id;
	}

	/**
	 * Check whether comment was updated or not.
	 *
	 * @param mixed $comment Comment to be check. It could be ID or instance of WP_Comment.
	 *
	 * @return bool
	 */
	public static function is_updated( $comment ) {
		$retrieved_comment = get_comment( $comment );

		if ( null === $retrieved_comment ) {
			return false;
		}

		$unix_timestamp = get_comment_meta( $retrieved_comment->comment_ID, self::META_UPDATED_AT, true );

		if ( empty( $unix_timestamp ) ) {
			return false;
		}

		return is_numeric( $unix_timestamp );
	}
}
