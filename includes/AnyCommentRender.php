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
		echo <<<EOT
<style>
$css_styles
</style>
EOT;
	}

	/**
	 * Enqueue required core assets and scripts.
	 */
	public function enqueue_scripts() {
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
