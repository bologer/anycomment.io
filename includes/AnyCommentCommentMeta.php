<?php

/**
 * Class AnyCommentCommentMeta is used to handle comment meta of comments.
 */
class AnyCommentCommentMeta {

	/**
	 * Comment attachments.
	 */
	const META_ATTACHMENTS = 'anycomment_attachments';

	/**
	 * Get attachments as plain string or array.
	 *
	 * @param int $comment_id Comment ID.
	 *
	 * @param bool $plainText If true, plain text will be returned, false will return array when value is not empty.
	 *
	 * @return string|array string when returned as plain text and array when returned as array.
	 */
	public static function getAttachments( $comment_id, $plainText = true ) {
		$value = get_comment_meta( $comment_id, self::META_ATTACHMENTS, true );

		if ( empty( $value ) ) {
			return [];
		}

		if ( $plainText ) {
			return $value;
		}

		return json_decode( $value );
	}

	/**
	 * Add attachment for a comment.
	 *
	 * @param int $comment_id
	 * @param mixed $value Value of attachments. Can be JSON string or array. Array will be JSON encoded.
	 *
	 * @return int|bool Meta ID on success, false on failure.
	 */
	public static function addAttachments( $comment_id, $value ) {

		if ( ! is_string( $value ) ) {
			$value = json_encode( $value );
		}

		return add_comment_meta( $comment_id, self::META_ATTACHMENTS, $value );
	}

	/**
	 * Update comment attachments.
	 *
	 * @param int $comment_id Comment ID.
	 * @param mixed $value Value of attachments. Can be JSON string or array. Array will be JSON encoded.
	 *
	 * @return int|bool Meta ID on success, false on failure.
	 */
	public static function updateAttachments( $comment_id, $value ) {

		if ( ! is_string( $value ) ) {
			$value = json_encode( $value );
		}

		return update_comment_meta( $comment_id, self::META_ATTACHMENTS, $value );
	}
}