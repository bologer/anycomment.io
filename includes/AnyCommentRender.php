<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentRender' ) ) :
	/**
	 * AnyCommentRender helps to render comments on client side.
	 */
	class AnyCommentRender {
		/**
		 * Default comment limit.
		 */
		const LIMIT = 20;

		/**
		 * Sort old.
		 */
		const SORT_OLD = 'old';

		/**
		 * Sort new.
		 */
		const SORT_NEW = 'new';

		/**
		 * AC_Render constructor.
		 */
		public function __construct() {
			if ( AnyCommentGenericSettings::isEnabled() && AnyCommentSocialSettings::hasAnySocial() ) {
				add_filter( 'comments_template', [ $this, 'render_iframe' ] );

				add_action( 'wp_ajax_iframe_comments', [ $this, 'iframe_comments' ] );
				add_action( 'wp_ajax_nopriv_iframe_comments', [ $this, 'iframe_comments' ] );

				add_action( 'wp_ajax_render_comments', [ $this, 'render_comments' ] );
				add_action( 'wp_ajax_nopriv_render_comments', [ $this, 'render_comments' ] );
			}
		}

		/**
		 * Make custom template for comments.
		 * @return string
		 */
		public function render_iframe() {
			wp_enqueue_script(
				'anycomment-iframeResizer',
				AnyComment()->plugin_url() . '/assets/js/iframeResizer.min.js',
				[],
				1.0
			);

			return ANYCOMMENT_ABSPATH . 'templates/iframe.php';
		}

		/**
		 * Render comments inside of the iframe.
		 *
		 * Should be requested by AJAX.
		 */
		public function iframe_comments() {
			if ( ! wp_verify_nonce( $_GET['nonce'], 'iframe_comments' ) ) {
				wp_die();
			}

			include ANYCOMMENT_ABSPATH . 'templates/comments.php';

			if ( AnyComment()->errors->hasErrors() ) {
				AnyComment()->errors->cleanErrors();
			}

			die();
		}

		/**
		 * Get comments.
		 *
		 * @param null|int $postId Post ID to check comments for. Avoid then get_the_ID() will be used to get id.
		 * @param int $limit Limit number of comments to load.
		 * @param string $sort Sorting type. New or old. Default is new.
		 *
		 * @return array|null NULL when there are no comments for post.
		 */
		public function get_comments( $postId = null, $limit = null, $sort = null ) {

			if ( $limit === null || empty( $limit ) ) {
				$limit = AnyCommentGenericSettings::getPerPage();
			}

			if ( $sort === null || ( $sort !== self::SORT_NEW && $sort !== self::SORT_OLD ) ) {
				$sort = self::SORT_NEW;
			}

			$options = [
				'post_id'        => $postId === null ? get_the_ID() : $postId,
				'parent'         => 0,
				'comment_status' => 1,
				'number'         => $limit,
				'orderby'        => 'comment_ID',
				'order'          => $sort === self::SORT_NEW ? 'DESC' : 'ASC'
			];

			$comments = get_comments( $options );

			return count( $comments ) > 0 ? $comments : null;
		}

		/**
		 * Get parent child comments.
		 *
		 * @param int $commentId Parent comment id.
		 * @param null|int $postId Post ID to check comments for. Avoid then get_the_ID() will be used to get id.
		 *
		 * @return array|null NULL when there are no comments for post.
		 */
		public function get_child_comments( $commentId, $postId = null ) {
			if ( $commentId === null ) {
				return null;
			}

			$comments = get_comments( [
				'parent'  => $commentId,
				'post_id' => $postId === null ? get_the_ID() : $postId
			] );

			return count( $comments ) > 0 ? $comments : null;
		}

		/**
		 * Use to get freshest list of comment list.
		 */
		public function render_comments() {
			check_ajax_referer( 'load-comments-nonce' );

			$postId = sanitize_text_field( $_POST['postId'] );
			$limit  = sanitize_text_field( $_POST['limit'] );
			$sort   = sanitize_text_field( $_POST['sort'] );

			if ( empty( $postId ) ) {
				echo AnyComment()->json_error( __( "No post ID specified", 'anycomment' ) );
				wp_die();
			}

			if ( ! get_post_status( $postId ) ) {
				echo AnyComment()->json_error( sprintf( __( "Unable to find post with ID #%s", 'anycomment' ), $postId ) );
				wp_die();
			}

			do_action( 'anycomment_comments', $postId, $limit, $sort );
			wp_die();
		}

		/**
		 * Get comment count.
		 *
		 * @param int $post_id Post ID.
		 *
		 * @return string
		 */
		public function get_comment_count( $post_id ) {
			return sprintf( __( '%s Comments', 'anycomment' ), get_comments_number( $post_id ) );
		}

		/**
		 * Check whether it is too old to edit (update/delete) comment.
		 *
		 * @param WP_Comment $comment Comment to be checked.
		 * @param int $minutes Number of minutes comment allow to be edited.
		 *
		 * Note: if `$minutes` is below 5, it will be set to 5 as it is the default value.
		 *
		 * @return bool
		 */
		public function is_old_to_edit( $comment, $minutes = 5 ) {
			$commentTime = strtotime( $comment->comment_date_gmt );

			if ( (int) $minutes < 5 ) {
				$minutes = 5;
			}

			$secondsToEdit = (int) $minutes * 60;

			return time() > ( $commentTime + $secondsToEdit );
		}

		/**
		 * Check whether current user has ability to edit comment.
		 *
		 * @param $comment
		 *
		 * @return bool
		 */
		public function can_edit_comment( $comment ) {
			if ( current_user_can( 'moderate_comments' ) ||
			     current_user_can( 'edit_comment', $comment->comment_ID ) ) {
				return true;
			}

			if ( $this->is_old_to_edit( $comment ) ) {
				return false;
			}

			$user = wp_get_current_user();

			if ( ! $user instanceof WP_User ) {
				return false;
			}

			return (int) $user->ID === (int) $comment->user_id;
		}
	}
endif;