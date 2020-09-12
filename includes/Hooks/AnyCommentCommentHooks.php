<?php

namespace AnyComment\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_Comment;
use AnyComment\Base\BaseObject;
use AnyComment\Api\AnyCommentServiceSyncIn;
use AnyComment\Integrations\AnyCommentBuddyPress;

use AnyComment\AnyCommentCommentMeta;
use AnyComment\Models\AnyCommentLikes;
use AnyComment\Models\AnyCommentEmailQueue;
use AnyComment\Models\AnyCommentSubscriptions;
use AnyComment\Models\AnyCommentUploadedFiles;
use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Cache\AnyCommentRestCacheManager;
use AnyComment\Admin\AnyCommentIntegrationSettings;

/**
 * Class AnyCommentCommentHooks is used to control hooks related to comments.
 */
class AnyCommentCommentHooks extends BaseObject {
	/**
	 * @inheritDoc
	 */
	public function init() {
		// Should delete files, likes, etc before comment is deleted
		// as WP by default remove comment meta, which is required to determine attachments IDs
		add_action( 'delete_comment', [ $this, 'process_deleted_comment' ], 10, 2 );

		// After comment was updated
		add_action( 'edit_comment', [ $this, 'process_edit_comment' ], 10, 2 );


		add_action( 'wp_insert_comment', [ $this, 'process_new_comment' ], 9, 2 );

		remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
		remove_filter( 'pre_comment_content', 'wp_filter_kses' );

		// Extend allowed HTML tags to the needs of visual editor
		add_filter( 'pre_comment_content', [ $this, 'kses_allowed_html_for_quill' ], 9 );
	}

	/**
	 * Extend list of allowed HTML tags.
	 *
	 * @param string $data Post content to filter, expected to be escaped with slashes
	 *
	 * @return mixed
	 */
	public function kses_allowed_html_for_quill( $data ) {
		global $allowedtags;

		$allowedtags = [];

		$allowedtags['p']          = [];
		$allowedtags['a']          = [ 'href' => true, 'target' => true, 'rel' => true ];
		$allowedtags['ul']         = [];
		$allowedtags['ol']         = [];
		$allowedtags['blockquote'] = [ 'class' => true ];
		$allowedtags['code']       = [];
		$allowedtags['li']         = [];
		$allowedtags['b']          = [];
		$allowedtags['i']          = [];
		$allowedtags['u']          = [];
		$allowedtags['strong']     = [];
		$allowedtags['em']         = [];
		$allowedtags['br']         = [];
		$allowedtags['img']        = [ 'class' => true, 'src' => true, 'alt' => true ];
		$allowedtags['figure']     = [];
		$allowedtags['iframe']     = [];

		return $data;
	}

	/**
	 * Process comment which will be deleted soon in order to clean-up after it.
	 *
	 * @param int $comment_id Comment ID.
	 * @param WP_Comment $comment Comment object.
	 *
	 * @return bool false when comment should not processed.
	 */
	public function process_new_comment( $comment_id, $comment ) {

		$should_notify = true;

		// Check Akismet
		$is_akismet_active = AnyCommentIntegrationSettings::is_akismet_active();
		if ( $is_akismet_active ) {
			$last_comment = \Akismet::get_last_comment();

			// When false returned from Akismet API it means it is spam comment
			if ( is_array( $last_comment ) && isset( $last_comment['akismet_result'] ) && $last_comment['akismet_result'] == 'false' ) {
				$should_notify = false;
			}
		}

		if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			$buddyPress = new AnyCommentBuddyPress();

			$text = sprintf(
				'%s - %s',
				$comment->comment_content,
				get_permalink( $comment->comment_post_ID )
			);

			$buddyPress->sendMentionsByText( $text );
		}

		if ( ! in_array( $comment->comment_type, [ 'comment' ] ) ) {
			$should_notify = false;
		}

		$meta = get_comment_meta( $comment->comment_ID, AnyCommentServiceSyncIn::getCommentImportedMetaKey() );

		if ( ! empty( $meta ) ) {
			$should_notify = false;
		}

		if ( $should_notify ) {
			// Notify subscribers
			AnyCommentSubscriptions::notify_by( $comment );

			// Notify on comment reply
			if ( AnyCommentGenericSettings::is_notify_on_new_reply() ) {
				AnyCommentEmailQueue::add_as_reply( $comment );
			}

			// Notify admin
			if ( AnyCommentGenericSettings::is_notify_admin() ) {
				AnyCommentEmailQueue::add_as_admin_notification( $comment );
			}
		}

		if ( empty( $comment->comment_type ) ) {
			// Flush post comment count cache
			AnyCommentRestCacheManager::flushPostCommentCount( $comment->comment_post_ID );
		}

		return true;
	}


	/**
	 * Process comment which will be deleted soon in order to clean-up after it.
	 *
	 * @param int $comment_id Comment ID.
	 * @param WP_Comment $comment Comment object.
	 *
	 * @return bool
	 */
	public function process_deleted_comment( $comment_id, $comment = null ) {

		if ( $comment === null ) {
			$comment = get_comment( $comment_id );
		}

		if ( empty( $comment ) ) {
			return false;
		}

		// Delete likes of a comment
		AnyCommentLikes::deleteLikes( $comment_id );

		// Flush post comment count cache
		AnyCommentRestCacheManager::flushPostCommentCount( $comment->comment_post_ID );

		// Delete attached files
		$comment_metas = AnyCommentCommentMeta::get_attachments( $comment_id );

		if ( ! empty( $comment_metas ) ) {
			foreach ( $comment_metas as $comment_meta ) {
				AnyCommentUploadedFiles::delete( $comment_meta->file_id );
			}
		}

		return true;
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
