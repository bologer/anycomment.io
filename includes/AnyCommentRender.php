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
			if ( AnyCommentGenericSettings::isEnabled() ) {
				add_filter( 'comments_template', [ $this, 'override_comment' ] );

				add_shortcode( 'anycomment', [ $this, 'override_comment' ] );
			}

			add_filter( 'logout_url', [ $this, 'logout_redirect' ], 10, 2 );
		}

		/**
		 * Custom logout URL to redirect user back to post on logout.
		 *
		 * @param string $logout_url Generated logout URL by WordPress.
		 * @param string|null $redirect Redirect URL.
		 *
		 * @since 0.0.52
		 *
		 * @return string
		 */
		function logout_redirect( $logout_url, $redirect ) {
			$permaLink = get_permalink();
			if ( $permaLink !== false && is_singular() ) {
				$query = parse_url( $logout_url, PHP_URL_QUERY );

				$hashedPermalink = sprintf( '%s#%s', $permaLink, 'comments' );

				if ( $permaLink ) {
					$logout_url .= sprintf( '%sredirect_to=%s', ( $query ? '&' : '?' ), $hashedPermalink );
				}
			}

			return $logout_url;
		}

		/**
		 * Make custom template for comments.
		 * @return string
		 */
		public function override_comment( $atts ) {

			$params = shortcode_atts( array(
				'include' => false,
			), $atts );

			$isInclude = $params['include'];

			if ( ! post_password_required() && comments_open() ) {
				wp_enqueue_script( 'anycomment-react', AnyComment()->plugin_url() . '/static/js/main.min.js', [], AnyComment()->version );

				if ( AnyCommentGenericSettings::isDesignCustom() ) {
					$url = AnyCommentGenericSettings::getCustomDesignStylesheetUrl();

					wp_enqueue_style( 'anycomment-custom-styles', $url, [], AnyComment()->version );
				} else {
					wp_enqueue_style( 'anycomment-styles', AnyComment()->plugin_url() . '/static/css/main.min.css', [], AnyComment()->version );
				}


				if ( strpos( AnyCommentGenericSettings::getDesignFontFamily(), 'Noto-Sans' ) !== false ) {
					wp_enqueue_style( 'anycomment-google-font', 'https://fonts.googleapis.com/css?family=Noto+Sans:400,700&amp;subset=cyrillic', [], AnyComment()->version );
				}

				$errors = AnyCommentSocialAuth::getErrors( true );
				AnyCommentSocialAuth::cleanErrors();

				$postId        = get_the_ID();
				$postPermalink = get_permalink( $postId );

				wp_localize_script( 'anycomment-react', 'anyCommentApiSettings', [
					'postId'       => $postId,
					'nonce'        => wp_create_nonce( 'wp_rest' ),
					'locale'       => get_locale(),
					'restUrl'      => esc_url_raw( rest_url( 'anycomment/v1/' ) ),
					'commentCount' => ( $res = get_comment_count( $postId ) ) !== null ? (int) $res['all'] : 0,
					'errors'       => $errors,
					'urls'         => [
						'logout'  => wp_logout_url(),
						'postUrl' => $postPermalink,
					],
					// Options from plugin
					'options'      => [
						'limit'                  => AnyCommentGenericSettings::getPerPage(),
						'isCopyright'            => AnyCommentGenericSettings::isCopyrightOn(),
						'socials'                => anycomment_login_with( false, get_permalink( $postId ) ),
						'theme'                  => AnyCommentGenericSettings::getTheme(),
						'sort_order'             => AnyCommentGenericSettings::getSortOrder(),
						'guestInputs'            => AnyCommentGenericSettings::getGuestFields( true ),
						'isShowProfileUrl'       => AnyCommentGenericSettings::isShowProfileUrl(),
						'isShowImageAttachments' => AnyCommentGenericSettings::isShowImageAttachments(),
						'isShowVideoAttachments' => AnyCommentGenericSettings::isShowVideoAttachments(),
						'isShowTwitterEmbeds'    => AnyCommentGenericSettings::isShowTwitterEmbeds(),
						'isLinkClickable'        => AnyCommentGenericSettings::isLinkClickable(),
						'userAgreementLink'      => AnyCommentGenericSettings::getUserAgreementLink(),
						'notifyOnNewComment'     => AnyCommentGenericSettings::isNotifyOnNewComment(),
						'intervalCommentsCheck'  => AnyCommentGenericSettings::getIntervalCommentsCheck(),
						'isLoadOnScroll'         => AnyCommentGenericSettings::isLoadOnScroll(),
						'isFormTypeAll'          => AnyCommentGenericSettings::isFormTypeAll(),
						'isFormTypeGuests'       => AnyCommentGenericSettings::isFormTypeGuests(),
						'isFormTypeSocials'      => AnyCommentGenericSettings::isFormTypeSocials(),
						'isGuestCanUpload'       => AnyCommentGenericSettings::isGuestCanUpload(),
						'fileMimeTypes'          => AnyCommentGenericSettings::getFileMimeTypes(),
						'fileLimit'              => AnyCommentGenericSettings::getFileLimit(),
						'fileMaxSize'            => AnyCommentGenericSettings::getFileMaxSize(),
						'fileUploadLimit'        => AnyCommentGenericSettings::getFileUploadLimit(),

						'reCaptchaOn'        => AnyCommentIntegrationSettings::isRecaptchaOn(),
						'reCaptchaUserAll'   => AnyCommentIntegrationSettings::isRecaptchaUserAll(),
						'reCaptchaUserGuest' => AnyCommentIntegrationSettings::isRecaptchaUserGuest(),
						'reCaptchaUserAuth'  => AnyCommentIntegrationSettings::isRecaptchaUserAuth(),
						'reCaptchaSiteKey'   => AnyCommentIntegrationSettings::getRecaptchaSiteKey(),
						'reCaptchaTheme'     => AnyCommentIntegrationSettings::getRecaptchaTheme(),
						'reCaptchaPosition'  => AnyCommentIntegrationSettings::getRecaptchaBadge(),
					],
					'user'         => AnyCommentUser::getSafeUser(),
					'i18'          => [
						'error_generic'                  => __( "Oops, something went wrong...", "anycomment" ),
						'loading'                        => __( 'Loading...', 'anycomment' ),
						'load_more'                      => __( "Load more", "anycomment" ),
						'button_send'                    => __( 'Send', 'anycomment' ),
						'button_save'                    => __( 'Save', 'anycomment' ),
						'button_reply'                   => __( 'Reply', 'anycomment' ),
						'sorting'                        => __( 'Sorting', 'anycomment' ),
						'sort_by'                        => __( 'Sort By', 'anycomment' ),
						'sort_oldest'                    => __( 'Oldest', 'anycomment' ),
						'sort_newest'                    => __( 'Newest', 'anycomment' ),
						'reply_to'                       => __( 'reply to', 'anycomment' ),
						'add_comment'                    => __( 'Your comment...', 'anycomment' ),
						'no_comments'                    => __( 'No comments to display', "anycomment" ),
						'footer_copyright'               => __( 'Add Anycomment to your site', 'anycomment' ),
						'reply'                          => __( 'Reply', 'anycomment' ),
						'edit'                           => __( 'Edit', 'anycomment' ),
						'delete'                         => __( 'Delete', 'anycomment' ),
						'cancel'                         => __( 'Cancel', 'anycomment' ),
						'quick_login'                    => __( 'Quick Login', 'anycomment' ),
						'logout'                         => __( 'Logout', 'anycomment' ),
						'new_comment_was_added'          => __( 'New comment was added', 'anycomment' ),
						'author'                         => __( 'Author', 'anycomment' ),
						'name'                           => __( 'Name', 'anycomment' ),
						'email'                          => __( 'Email', 'anycomment' ),
						'website'                        => __( 'Website', 'anycomment' ),
						'accept_user_agreement'          => sprintf(
							__( 'I accept the <a href="%s"%s>User Agreement</a>', 'anycomment' ),
							AnyCommentGenericSettings::getUserAgreementLink(),
							' target="_blank" rel="noopener noreferrer" '
						),
						'upload_file'                    => __( 'Upload file', 'anycomment' ),
						'file_upload_in_progress'        => __( "Uploading...", 'anycomment' ),
						'file_uploaded'                  => __( "Uploaded!", 'anycomment' ),
						'file_too_big'                   => __( "File %s is too big", 'anycomment' ),
						'file_limit'                     => sprintf( __( "You may upload %s file(s) at maximum", 'anycomment' ), AnyCommentGenericSettings::getFileLimit() ),
						'file_not_selected_or_extension' => __( "No file selected or select proper extension", 'anycomment' ),
						'read_more'                      => __( 'Read more', 'anycomment' ),
						'show_less'                      => __( 'Show less', 'anycomment' ),
					]
				] );
			}

			AnyCommentSocialAuth::cleanErrors();

			$path = ANYCOMMENT_ABSPATH . 'templates/comments.php';

			if ( $isInclude ) {
				return anycomment_get_template( 'comments' );
			}

			return $path;
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

			$secondsToEdit        = (int) $minutes * 60;
			$currentUnixTimeMysql = strtotime( current_time( 'mysql', true ) );

			return $currentUnixTimeMysql > ( $commentTime + $secondsToEdit );
		}

		/**
		 * Check whether current user has ability to edit comment.
		 *
		 * @param WP_Comment $comment
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