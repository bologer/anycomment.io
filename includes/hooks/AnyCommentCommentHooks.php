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
		// Should delete files, likes, etc before comment is deleted
		// as WP by default remove comment meta, which is required to determine attachments IDs
		add_action( 'delete_comment', [ $this, 'process_deleted_comment' ], 10, 2 );

		// Should drop comment cache after it was deleted, just in case
//		add_action( 'deleted_comment', [ $this, 'process_soft_comment' ], 10, 2 );

		// After comment was updated
		add_action( 'edit_comment', [ $this, 'process_edit_comment' ], 10, 2 );

		// After comment was trashed, marked as spam, etc
//		add_action( 'trashed_comment', [ $this, 'process_soft_comment' ], 10, 2 );
//		add_action( 'untrashed_comment', [ $this, 'process_soft_comment' ], 10, 2 );
//		add_action( 'spam_comment', [ $this, 'process_soft_comment' ], 10, 2 );
//		add_action( 'unspam_comment', [ $this, 'process_soft_comment' ], 10, 2 );

		// On comment status change
//		add_action( 'wp_set_comment_status', [ $this, 'process_set_status_comment' ], 10, 2 );

		add_action( 'wp_insert_comment', [ $this, 'process_new_comment' ], 10, 2 );


		// Extend allowed HTML tags to the needs of visual editor
		add_filter( 'pre_comment_content', [ $this, 'kses_allowed_html_for_quill' ], 16 );
	}

	/**
	 * Extend list of allowed HTML tags.
	 *
	 * @param $allowedtags
	 *
	 * @return mixed
	 */
	public function kses_allowed_html_for_quill( $comment_content ) {
		$allowedhtml['p']          = [];
		$allowedhtml['a']          = [ 'href' => true, 'target' => true, 'rel' => true ];
		$allowedhtml['ul']         = [];
		$allowedhtml['ol']         = [];
		$allowedhtml['blockquote'] = [ 'class' => true ];
		$allowedhtml['code']       = [];
		$allowedhtml['li']         = [];
		$allowedhtml['b']          = [];
		$allowedhtml['i']          = [];
		$allowedhtml['u']          = [];
		$allowedhtml['strong']     = [];
		$allowedhtml['em']         = [];
		$allowedhtml['br']         = [];
		$allowedhtml['img']        = [ 'class' => true, 'src' => true, 'alt' => true ];
		$allowedhtml['figure']     = [];
		$allowedhtml['iframe']     = [];

		return wp_kses( $comment_content, $allowedhtml );
	}

	/**
	 * Process comment which will be deleted soon in order to clean-up after it.
	 *
	 * @param int $comment_id Comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	public function process_new_comment( $comment_id, $comment ) {
		// Notify subscribers
		AnyCommentSubscriptions::notify_by( $comment );
	}

	/**
	 * Process comment which will be deleted soon in order to clean-up after it.
	 *
	 * @param int $comment_id Comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	public function process_deleted_comment( $comment_id, $comment ) {
		// Delete likes of a comment
		AnyCommentLikes::deleteLikes( $comment_id );

		// Delete attached files
		$comment_metas = AnyCommentCommentMeta::get_attachments( $comment_id );

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
	 * @param array $data Comment data.
	 */
	public function process_edit_comment( $comment_id, $data ) {
		// Mark comment as updated
		AnyCommentCommentMeta::mark_updated( $comment_id, $data );
	}

	/**
	 * Flush cache of comment after changing its status.
	 *
	 * @param int $comment_id Comment ID.
	 * @param int $status Status to be assigned.
	 */
	public function process_set_status_comment( $comment_id, $status ) {
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
	}
}