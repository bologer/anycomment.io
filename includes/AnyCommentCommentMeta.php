<?php

/**
 * Class AnyCommentCommentMeta is used to handle comment meta of comments.
 */
class AnyCommentCommentMeta {

	/**
	 * Comment attachments.
	 */
	const META_ATTACHMENT = 'anycomment_attachment';

	/**
	 * Delete attachments by meta id.
	 *
	 * @param int $meta_id Meta id.
	 *
	 * @return bool
	 */
	public static function deleteAttachment( $meta_id ) {
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
	public static function deleteAttachmentByFileId( $file_id ) {
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
	public static function existByAttachmentFileId( $file_id ) {
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
	public static function getAttachment( $meta_id, $comment_id = null, $withFile = true ) {
		global $wpdb;

		$sql = 'SELECT `meta`.*';

		if ( $withFile ) {
			$sql .= ', `files`.`ID` AS file_id, `files`.`post_ID` AS file_post_ID, `files`.`user_ID` AS file_user_ID, `files`.`type` AS file_type';
		}

		$sql .= " FROM `{$wpdb->commentmeta}` `meta`";

		if ( $withFile ) {
			$filesTable = AnyCommentUploadedFiles::tableName();
			$sql        .= " LEFT JOIN `$filesTable` `files` ON `meta`.`meta_value`=`files`.`id`";
		}

		$sql .= " WHERE `meta`.`meta_id` = %d";

		$preparedArray = [ $meta_id ];

		if ( $comment_id !== null ) {
			$preparedArray[] = $comment_id;
			$sql             .= ' AND `meta`.`comment_id` = %d';
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
	public static function getAttachments( $comment_id ) {
		global $wpdb;

		$commentTable = $wpdb->commentmeta;
		$filesTable   = AnyCommentUploadedFiles::tableName();

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
	public static function getAttachmentsForApi( $comment_id, $asObject = true ) {

		$res = static::getAttachments( $comment_id );

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
	public static function addAttachments( $comment_id, $attachments ) {
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
				if ( ! static::existByAttachmentFileId( $fileId ) ) {
					add_comment_meta( $comment_id, self::META_ATTACHMENT, $fileId );
				}
			}
		}

		return true;
	}
}