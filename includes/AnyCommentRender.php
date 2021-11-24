<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Base\BaseObject;
use AnyComment\Helpers\AnyCommentTemplate;
use AnyComment\Models\AnyCommentRating;
use AnyComment\Rest\AnyCommentSocialAuth;
use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Admin\AnyCommentIntegrationSettings;

/**
 * AnyCommentRender helps to render comments on client side.
 */
class AnyCommentRender extends BaseObject {
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
	public static $errors = null;

	/**
	 * @inheritDoc
	 */
	public function init() {
		if ( AnyCommentGenericSettings::is_enabled() ) {
			add_filter( 'comments_template', [ $this, 'override_comment' ], 999 );

			add_shortcode( 'anycomment', [ $this, 'shortcode_override' ] );

			add_filter( 'logout_url', [ $this, 'logout_redirect' ], 10, 2 );

			if ( ! AnyCommentIntegrationSettings::is_sass_comments_show() ) {
				add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			}

			add_filter( 'script_loader_tag', [ $this, 'add_async_to_bundle' ], 10, 2 );
		}
		add_action( 'wp_head', [ $this, 'enqueue_custom_css' ], 99 );

		self::$errors = AnyCommentSocialAuth::getErrors( true, true );
	}


	/**
	 * Custom logout URL to redirect user back to post on logout.
	 *
	 * @param string $logout_url Generated logout URL by WordPress.
	 *
	 * @return string
	 * @since 0.0.52
	 *
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
	 * Enqueue custom CSS styles.
	 */
	public function enqueue_custom_css() {

		if ( ! comments_open() || post_password_required() ) {
			return;
		}

		$css_styles = AnyCommentGenericSettings::get_editor_css();
		$html       = <<<EOT
<style>
$css_styles
</style>
EOT;

		return wp_kses( $html, [ 'style' => [] ] );
	}

	/**
	 * Enqueue required core assets and scripts.
	 */
	public function enqueue_scripts() {
		$plugin_url = ANYCOMMENT_DEBUG ?
			AnyComment()->plugin_url() . '/reactjs/dist/index.js' :
			AnyComment()->plugin_url() . '/static/js/main.min.js';

		wp_enqueue_script(
			'anycomment-js-bundle',
			$plugin_url,
			[],
			md5( AnyComment()->version ),
			true
		);


		if ( AnyCommentGenericSettings::is_design_custom() ) {
			wp_enqueue_style( 'anycomment-custom-styles', AnyCommentGenericSettings::get_custom_design_stylesheet_url(),
				[], md5( AnyComment()->version ) );
		} else {
			wp_enqueue_style( 'anycomment-styles', AnyComment()->plugin_url() . '/static/css/main.min.css', [],
				md5( AnyComment()->version ) );
		}


		if ( strpos( AnyCommentGenericSettings::get_design_font_family(), 'Noto-Sans' ) !== false ) {
			wp_enqueue_style( 'anycomment-google-font',
				'https://fonts.googleapis.com/css?family=Noto+Sans:400,700&subset=cyrillic&display=swap' );
		}

		$post_id        = get_the_ID();
		$post_permalink = get_permalink( $post_id );

		try {
			$comments_open = comments_open();
		} catch ( \Exception $exception ) {
			AnyCommentCore::logger()->error( sprintf(
				'Failed to check if possible to open comments as exception was thrown: %s in %s:%s',
				$exception->getMessage(),
				$exception->getFile(),
				$exception->getLine()
			) );
			$comments_open = false;
		}

		$comment_count = 0;

		$count_data = get_comment_count( $post_id );

		if ( $comment_count !== null ) {
			if ( ! current_user_can( 'moderate_comments' ) ) {
				$comment_count = $count_data['all'];
			} else {
				$comment_count = $count_data['approved'];
			}
		}

		// time added just in case so it is never cached
		wp_localize_script( 'anycomment-js-bundle', 'anyCommentApiSettings', [
			'postId'       => $post_id,
			'nonce'        => wp_create_nonce( 'wp_rest' ),
			'locale'       => get_locale(),
			'restUrl'      => esc_url_raw( rest_url( 'anycomment/v1/' ) ),
			'commentCount' => $comment_count,
			'errors'       => self::$errors,
			'user'         => AnyCommentUser::getSafeUser(),
			'urls'         => [
				'logout'  => wp_logout_url(),
				'postUrl' => $post_permalink,
			],
			'post'         => [
				'id'            => $post_id,
				'permalink'     => $post_permalink,
				'comments_open' => $comments_open,
			],
			'rating'       => [
				'value'    => AnyCommentRating::get_average_by_post( $post_id ),
				'count'    => AnyCommentRating::get_count_by_post( $post_id ),
				'hasRated' => AnyCommentRating::current_user_rated( $post_id, get_current_user_id() ),
			],
			// Options from plugin
			'options'      => [
				'limit'                  => AnyCommentGenericSettings::get_per_page(),
				'isCopyright'            => AnyCommentGenericSettings::is_copyright_on(),
				'socials'                => AnyCommentSocials::get_all( get_permalink( $post_id ) ),
				'sort_order'             => AnyCommentGenericSettings::get_sort_order(),
				'guestInputs'            => AnyCommentGenericSettings::get_guest_fields( true ),
				'isShowUpdatedInfo'      => AnyCommentGenericSettings::is_show_updated_info(),
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

				'commentRating' => AnyCommentGenericSettings::get_comment_rating(),
				'dateFormat'    => AnyCommentGenericSettings::get_datetime_format(),

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
				'comments_closed'                => __( 'Comments are closed.', 'anycomment' ),
				'subscribed'                     => is_user_logged_in() ?
					__( 'You were subscribed successfully', 'anycomment' ) :
					__( 'Check you email to confirm subscription', 'anycomment' ),
				'subscribe'                      => __( 'Subscribe', 'anycomment' ),
				'subscribe_pre_paragraph'        => is_user_logged_in() ?
					__( 'You may subscribe to new comments by clicking "Subscribe" button below:', 'anycomment' ) :
					__( 'You may subscribe to new comments for this post by entering your email below:', 'anycomment' ),
				'cancel'                         => __( 'Cancel', 'anycomment' ),
				'quick_login'                    => __( 'Quick Login', 'anycomment' ),
				'guest'                          => __( 'Guest', 'anycomment' ),
				'login'                          => __( 'Login', 'anycomment' ),
				'logout'                         => __( 'Logout', 'anycomment' ),
				'comment_waiting_moderation'     => __( 'Comment will be shown once reviewed by moderator.',
					'anycomment' ),
				'new_comment_was_added'          => __( 'New comment was added', 'anycomment' ),
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
				'file_limit'                     => sprintf( __( "You may upload %s file(s) at maximum", 'anycomment' ),
					AnyCommentGenericSettings::get_file_limit() ),
				'file_not_selected_or_extension' => __( "No file selected or select proper extension", 'anycomment' ),
				'read_more'                      => __( 'Read more', 'anycomment' ),
				'show_less'                      => __( 'Show less', 'anycomment' ),
				'hide_this_message'              => __( 'Hide this message', 'anycomment' ),
				'login_with'                     => __( 'Login with', 'anycomment' ),
				'or_as_guest'                    => __( 'or as guest:', 'anycomment' ),
				'comments_count'                 => __( 'Comments:', 'anycomment' ),

				/**
				 * Lightbox
				 */
				'lighbox_close'                  => __( 'Close (Esc)', 'anycomment' ),
				'lighbox_left_arrow'             => __( 'Previous (Left arrow key)', 'anycomment' ),
				'lighbox_right_arrow'            => __( 'Next (Right arrow key)', 'anycomment' ),
				'lighbox_image_count_separator'  => __( ' of ', 'anycomment' ),
			],
		] );

		return true;
	}

	/**
	 * Make custom template for comments.
	 *
	 * @param string $original_template Original (theme's) template path.
	 *
	 * @return string
	 */
	public function override_comment( $original_template ) {
		if ( ! is_singular() || ! comments_open() || post_password_required() || AnyCommentIntegrationSettings::is_sass_comments_show() ) {
			remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 11 );
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_product() && ! AnyCommentIntegrationSettings::is_replace_woocommerce_review_form() ) {
			return $original_template;
		}

		return ANYCOMMENT_ABSPATH . str_replace( '/', DIRECTORY_SEPARATOR, '/templates/override-comments.php' );
	}

	/**
	 * Make custom template for shortcode comments.
	 *
	 * @return string
	 */
	public function shortcode_override() {
		if ( ! is_singular() || AnyCommentIntegrationSettings::is_sass_comments_show() ) {
			remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		return AnyCommentTemplate::render( 'shortcode-comments' );
	}
}
