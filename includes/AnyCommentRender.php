<?php

namespace AnyComment;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_Comment;
use WP_User;

use AnyComment\Helpers\AnyCommentTemplate;
use AnyComment\Models\AnyCommentRating;
use AnyComment\Rest\AnyCommentSocialAuth;
use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Admin\AnyCommentIntegrationSettings;

/**
 * AnyCommentRender helps to render comments on client side.
 */
class AnyCommentRender {
	/**
	 * Sort old.
	 */
	const SORT_DESC = 'desc';

	/**
	 * Sort new.
	 */
	const SORT_ASC = 'asc';

	/**
	 * @var array|null Array list of error when there are such.
	 */
	public $errors = null;

	/**
	 * AC_Render constructor.
	 */
	public function __construct() {
		if ( AnyCommentGenericSettings::is_enabled() ) {
			add_filter( 'comments_template', [ $this, 'override_comment' ] );

			add_shortcode( 'anycomment', [ $this, 'override_comment' ] );

		}

		add_filter( 'logout_url', [ $this, 'logout_redirect' ], 10, 2 );

		add_filter( 'script_loader_tag', [ $this, 'add_async_to_bundle' ], 10, 2 );

		$this->errors = AnyCommentSocialAuth::getErrors();
	}


	/**
	 * Custom logout URL to redirect user back to post on logout.
	 *
	 * @param string $logout_url Generated logout URL by WordPress.
	 *
	 * @since 0.0.52
	 *
	 * @return string
	 */
	function logout_redirect( $logout_url ) {
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
	 * Async loading of main JavaScript bundle.
	 *
	 * @param $tag
	 * @param $handle
	 *
	 * @return mixed
	 * @since 0.0.66
	 */
	function add_async_to_bundle( $tag, $handle ) {
		if ( 'anycomment-js-bundle' !== $handle ) {
			return $tag;
		}

		return str_replace( ' src', ' async="async" src', $tag );
	}

	/**
	 * Make custom template for comments.
	 *
	 * @param array $atts List of options applied if used as shortcode.
	 *
	 * @return string
	 */
	public function override_comment( $atts ) {

		$params = shortcode_atts( array(
			'include' => false,
		), $atts );

		$isInclude = $params['include'];

		if ( ! post_password_required() && comments_open() ) {
			wp_enqueue_script( 'anycomment-js-bundle', AnyComment()->plugin_url() . '/static/js/main.min.js', [], md5( AnyComment()->version ) );

			if ( AnyCommentGenericSettings::is_design_custom() ) {
				$url = AnyCommentGenericSettings::get_custom_design_stylesheet_url();

				wp_enqueue_style( 'anycomment-custom-styles', $url, [], md5( AnyComment()->version ) );
			} else {
				wp_enqueue_style( 'anycomment-styles', AnyComment()->plugin_url() . '/static/css/main.min.css', [], md5( AnyComment()->version ) );
			}


			if ( strpos( AnyCommentGenericSettings::get_design_font_family(), 'Noto-Sans' ) !== false ) {
				wp_enqueue_style( 'anycomment-google-font', 'https://fonts.googleapis.com/css?family=Noto+Sans:400,700&amp;subset=cyrillic' );
			}

			$postId        = get_the_ID();
			$postPermalink = get_permalink( $postId );

			wp_localize_script( 'anycomment-js-bundle', 'anyCommentApiSettings', [
				'postId'       => $postId,
				'nonce'        => wp_create_nonce( 'wp_rest' ),
				'locale'       => get_locale(),
				'restUrl'      => esc_url_raw( rest_url( 'anycomment/v1/' ) ),
				'commentCount' => ( $res = get_comment_count( $postId ) ) !== null ? (int) $res['all'] : 0,
				'errors'       => $this->errors,
				'urls'         => [
					'logout'  => wp_logout_url(),
					'postUrl' => $postPermalink,
				],
				'rating'       => [
					'value'    => AnyCommentRating::get_average_by_post( $postId ),
					'count'    => AnyCommentRating::get_count_by_post( $postId ),
					'hasRated' => AnyCommentRating::current_user_rated( $postId, get_current_user_id() )
				],
				// Options from plugin
				'options'      => [
					'limit'                  => AnyCommentGenericSettings::get_per_page(),
					'isCopyright'            => AnyCommentGenericSettings::is_copyright_on(),
					'socials'                => AnyCommentSocials::getAll(get_permalink( $postId )),
					'sort_order'             => AnyCommentGenericSettings::get_sort_order(),
					'guestInputs'            => AnyCommentGenericSettings::get_guest_fields( true ),
					'isNotifySubscribers'    => AnyCommentGenericSettings::is_notify_subscribers(),
					'isShowProfileUrl'       => AnyCommentGenericSettings::is_show_profile_url(),
					'isShowImageAttachments' => AnyCommentGenericSettings::is_show_image_attachments(),
					'isShowVideoAttachments' => AnyCommentGenericSettings::is_show_video_attachments(),
					'isShowTwitterEmbeds'    => AnyCommentGenericSettings::is_show_twitter_embeds(),
					'isModerateFirst'        => AnyCommentGenericSettings::is_moderate_first(),
					'userAgreementLink'      => AnyCommentGenericSettings::get_user_agreement_link(),
					'notifyOnNewComment'     => AnyCommentGenericSettings::is_notify_on_new_comment(),
					'intervalCommentsCheck'  => AnyCommentGenericSettings::get_interval_comments_check(),
					'isLoadOnScroll'         => AnyCommentGenericSettings::is_load_on_scroll(),
					'isFormTypeAll'          => AnyCommentGenericSettings::is_form_type_all(),
					'isFormTypeGuests'       => AnyCommentGenericSettings::is_form_type_guests(),
					'isFormTypeSocials'      => AnyCommentGenericSettings::is_form_type_socials(),
					'isFormTypeWordpress'    => AnyCommentGenericSettings::is_form_type_wordpress(),
					'isFileUploadAllowed'    => AnyCommentGenericSettings::is_file_upload_allowed(),
					'isGuestCanUpload'       => AnyCommentGenericSettings::is_guest_can_upload(),
					'fileMimeTypes'          => AnyCommentGenericSettings::get_file_mime_types(),
					'fileLimit'              => AnyCommentGenericSettings::get_file_limit(),
					'fileMaxSize'            => AnyCommentGenericSettings::get_file_max_size(),
					'fileUploadLimit'        => AnyCommentGenericSettings::get_file_upload_limit(),
					'isRatingOn'             => AnyCommentGenericSettings::is_rating_on(),
					'isReadMoreOn'           => AnyCommentGenericSettings::is_read_more_on(),

					'isEditorOn'           => AnyCommentGenericSettings::is_editor_toolbar_on(),
					'editorToolbarOptions' => AnyCommentGenericSettings::get_editor_toolbar_options(),

					'reCaptchaOn'        => AnyCommentIntegrationSettings::is_recaptcha_active(),
					'reCaptchaUserAll'   => AnyCommentIntegrationSettings::is_recaptcha_user_all(),
					'reCaptchaUserGuest' => AnyCommentIntegrationSettings::is_recaptcha_user_guest(),
					'reCaptchaUserAuth'  => AnyCommentIntegrationSettings::is_recaptcha_user_auth(),
					'reCaptchaSiteKey'   => AnyCommentIntegrationSettings::get_recaptcha_site_key(),
					'reCaptchaTheme'     => AnyCommentIntegrationSettings::get_recaptcha_theme(),
					'reCaptchaPosition'  => AnyCommentIntegrationSettings::get_recaptcha_badge(),


				],
				'user'         => AnyCommentUser::getSafeUser(),
				'i18'          => [
					'error_generic'                  => __( "Oops, something went wrong...", "anycomment" ),
					'loading'                        => __( 'Loading...', 'anycomment' ),
					'load_more'                      => __( "Load more", "anycomment" ),
					'waiting_moderation'             => __( "Waiting moderation", "anycomment" ),
					'edited'                         => __( "Edited", "anycomment" ),
					'button_send'                    => __( 'Send', 'anycomment' ),
					'button_save'                    => __( 'Save', 'anycomment' ),
					'button_reply'                   => __( 'Reply', 'anycomment' ),
					'sorting'                        => __( 'Sorting', 'anycomment' ),
					'sort_by'                        => __( 'Sort by', 'anycomment' ),
					'sort_oldest'                    => __( 'oldest', 'anycomment' ),
					'sort_newest'                    => __( 'newest', 'anycomment' ),
					'reply_to'                       => __( 'reply to', 'anycomment' ),
					'editing'                        => __( 'editing', 'anycomment' ),
					'add_comment'                    => __( 'Your comment...', 'anycomment' ),
					'no_comments'                    => __( 'No comments to display', "anycomment" ),
					'footer_copyright'               => __( 'Add Anycomment to your site', 'anycomment' ),
					'reply'                          => __( 'Reply', 'anycomment' ),
					'edit'                           => __( 'Edit', 'anycomment' ),
					'delete'                         => __( 'Delete', 'anycomment' ),
					'subscribed'                     => __( 'You were subscribed successfully', 'anycomment' ),
					'subscribe'                      => __( 'Subscribe', 'anycomment' ),
					'subscribe_pre_paragraph'        => __( 'You may subscribe to comments for this post by providing your email address:', 'anycomment' ),
					'cancel'                         => __( 'Cancel', 'anycomment' ),
					'quick_login'                    => __( 'Quick Login', 'anycomment' ),
					'guest'                          => __( 'Guest', 'anycomment' ),
					'login'                          => __( 'Login', 'anycomment' ),
					'logout'                         => __( 'Logout', 'anycomment' ),
					'comment_waiting_moderation'     => __( 'Comment will be shown once reviewed by moderator.', 'anycomment' ),
					'author'                         => __( 'Author', 'anycomment' ),
					'name'                           => __( 'Name', 'anycomment' ),
					'email'                          => __( 'Email', 'anycomment' ),
					'website'                        => __( 'Website', 'anycomment' ),
					'already_rated'                  => __( 'You have already rated', 'anycomment' ),
					'accept_user_agreement'          => sprintf(
						__( 'I accept the <a href="%s"%s>User Agreement</a>', 'anycomment' ),
						AnyCommentGenericSettings::get_user_agreement_link(),
						' target="_blank" '
					),
					'upload_file'                    => __( 'Upload file', 'anycomment' ),
					'file_upload_in_progress'        => __( "Uploading...", 'anycomment' ),
					'file_uploaded'                  => __( "Uploaded!", 'anycomment' ),
					'file_too_big'                   => __( "File %s is too big", 'anycomment' ),
					'file_limit'                     => sprintf( __( "You may upload %s file(s) at maximum", 'anycomment' ), AnyCommentGenericSettings::get_file_limit() ),
					'file_not_selected_or_extension' => __( "No file selected or select proper extension", 'anycomment' ),
					'read_more'                      => __( 'Read more', 'anycomment' ),
					'show_less'                      => __( 'Show less', 'anycomment' ),
					'login_with'                     => __( 'Login with', 'anycomment' ),
					'or_as_guest'                    => sprintf( __( 'or as %sguest%s', 'anycomment' ), '<span>', '</span>' ),

					/**
					 * Lightbox
					 */
					'lighbox_close'                  => __( 'Close (Esc)', 'anycomment' ),
					'lighbox_left_arrow'             => __( 'Previous (Left arrow key)', 'anycomment' ),
					'lighbox_right_arrow'            => __( 'Next (Right arrow key)', 'anycomment' ),
					'lighbox_image_count_separator'  => __( ' of ', 'anycomment' ),
				]
			] );
		}

		$path = ANYCOMMENT_ABSPATH . 'templates/comments.php';

		if ( $isInclude ) {
			return AnyCommentTemplate::render( 'comments' );
		}

		return $path;
	}


	/**
	 * Get comment count.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	public function get_comment_count( $post_id ) {
		$count = get_comments_number( $post_id );

		return sprintf( _nx( '%s comment', '%s comments', $count, 'REST API Comments Count', 'anycomment' ), number_format_i18n( $count ) );
	}

	/**
	 * Check whether it is too old to edit (update/delete) comment.
	 *
	 * @param WP_Comment $comment Comment to be checked.
	 *
	 * @return bool
	 */
	public function is_old_to_edit( $comment ) {
		$commentTime = strtotime( $comment->comment_date_gmt );

		$minutes = AnyCommentGenericSettings::get_comment_update_time();

		if ( $minutes === 0 ) {
			return false;
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