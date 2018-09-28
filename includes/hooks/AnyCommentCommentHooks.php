<?php

/**
 * Class AnyCommentCommentHooks is used to control hooks related to comments.
 */
class AnyCommentCommentHooks {

	/**
	 * AnyCommentCommentHooks constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init method of all related hooks.
	 */
	private function init() {
		// After comment was deleted
		add_action( 'deleted_comment', [ $this, 'process_deleted_comment' ], 10, 2 );

		// After comment was updated
		add_action( 'edit_comment', [ $this, 'process_edit_comment' ], 10, 2 );

		// After comment was trashed, marked as spam, etc
		add_action( 'trashed_comment', [ $this, 'process_soft_comment' ], 10, 2 );
		add_action( 'untrashed_comment', [ $this, 'process_soft_comment' ], 10, 2 );
		add_action( 'spam_comment', [ $this, 'process_soft_comment' ], 10, 2 );
		add_action( 'unspam_comment', [ $this, 'process_soft_comment' ], 10, 2 );

		// On comment status change
		add_action( 'wp_set_comment_status', [ $this, 'process_set_status_comment' ], 10, 2 );
	}

	/**
	 * Process already deleted comment to clean-up.
	 *
	 * @param int $comment_id Comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	public function process_deleted_comment( $comment_id, $comment ) {
		// Need to drop comments cache
		\anycomment\cache\rest\AnyCommentRestCacheManager::flushComment( $comment->comment_post_ID, $comment_id );

		// Delete likes of a comment
		AnyCommentLikes::deleteLikes( $comment_id );

		// Delete attached files
		$comment_metas = AnyCommentCommentMeta::getAttachments( $comment_id );

		if ( ! empty( $comment_metas ) ) {
			foreach ( $comment_metas as $comment_meta ) {
				AnyCommentUploadedFiles::delete( $comment_meta->file_id );
			}
		}
	}

	/**
	 * Drops cache of updated comment.
	 *
	 * @param int $comment_id Comment ID.
	 * @param mixed $data Comment data.
	 */
	public function process_edit_comment( $comment_id, $data ) {

		$comment = get_comment( $comment_id );

		if ( $comment !== null ) {
			// Need to drop comments cache
			\anycomment\cache\rest\AnyCommentRestCacheManager::flushComment( $comment->comment_post_ID, $comment_id );
		}
	}

	/**
	 * Flush cache of comment after changing its status.
	 *
	 * @param int $comment_id Comment ID.
	 * @param int $status Status to be assigned.
	 */
	public function process_set_status_comment( $comment_id, $status ) {

		// Get cache object, as `wp_set_comment_status` hook does not provide it
		$comment = get_comment( $comment_id );

		if ( $comment !== null ) {
			// Just flush cache for the moment
			$this->process_soft_comment( $comment_id, $comment );
		}
	}

	/**
	 * Soft touch on the comment, such as flushing, etc.
	 *
	 * Primarily used for (un)span, (un)trash comment, etc.
	 *
	 * @param int $comment_id Comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	public function process_soft_comment( $comment_id, $comment ) {
		// Need to drop comments cache
		\anycomment\cache\rest\AnyCommentRestCacheManager::flushComment( $comment->comment_post_ID, $comment_id );
	}
}