<?php

namespace AnyComment\Admin;

use AnyComment\Options\AnyCommentOptionManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * AC_AdminSettingPage helps to process generic plugin settings.
 */
class AnyCommentIntegrationSettings extends AnyCommentOptionManager {

	/**
	 * Integration with Akismet.
	 * @link https://wordpress.org/plugins/akismet/
	 */
	const OPTION_AKISMET = 'option_akismet_toggle';

	/**
	 * Integration with WP User Avatar.
	 * @link https://wordpress.org/plugins/wp-user-avatar/
	 */
	const OPTION_WP_USER_AVATAR = 'option_wp_user_avatar';

	/**
	 * reCaptcha toggle (on/off).
	 */
	const OPTION_RECAPTCHA_TOGGLE = 'option_recaptcha_toggle';

	/**
	 * reCaptcha user (for whom it will shown).
	 */
	const OPTION_RECAPTCHA_USER = 'option_recaptcha_user';

	/**
	 * reCaptcha shown to all.
	 */
	const OPTION_RECAPTCHA_USER_ALL = 'option_recaptcha_user_all';

	/**
	 * reCaptcha shown to guest users only.
	 */
	const OPTION_RECAPTCHA_USER_GUEST = 'option_recaptcha_user_guest';

	/**
	 * reCaptcha shown to logged in users only.
	 */
	const OPTION_RECAPTCHA_USER_AUTH = 'option_recaptcha_user_auth';

	/**
	 * reCaptcha site key.
	 */
	const OPTION_RECAPTCHA_SITE_KEY = 'option_recaptcha_site_key';

	/**
	 * reCaptcha site secret.
	 */
	const OPTION_RECAPTCHA_SITE_SECRET = 'option_recaptcha_secret_key';

	/**
	 * reCaptcha theme (light or dark).
	 */
	const OPTION_RECAPTCHA_THEME = 'option_recaptcha_theme';

	/**
	 * Light theme option.
	 */
	const OPTION_RECAPTCHA_THEME_LIGHT = 'light';

	/**
	 * Dark theme option.
	 */
	const OPTION_RECAPTCHA_THEME_DARK = 'dark';


	/**
	 * reCaptcha theme (bottomright, bottomleft or inline).
	 */
	const OPTION_RECAPTCHA_BADGE = 'option_recaptcha_badge';

	const OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT = 'bottomright';
	const OPTION_RECAPTCHA_BADGE_BOTTOM_LEFT = 'bottomleft';
	const OPTION_RECAPTCHA_BADGE_BOTTOM_INLINE = 'inline';

	/**
	 * @inheritdoc
	 */
	protected $option_group = 'anycomment-integration-group';
	/**
	 * @inheritdoc
	 */
	protected $option_name = 'anycomment-integration';

	/**
	 * @inheritdoc
	 */
	protected $page_slug = 'anycomment-integration';

	/**
	 * @inheritdoc
	 */
	protected $field_options = [
		'wrapper_class' => 'cell anycomment-form-wrapper__field',
	];

	/**
	 * @inheritdoc
	 */
	protected $section_options = [
		'wrapper' => '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab" id="{id}">{content}</div>'
	];

	/**
	 * @inheritdoc
	 */
	protected $default_options = [
		self::OPTION_RECAPTCHA_THEME => self::OPTION_RECAPTCHA_THEME_LIGHT,
		self::OPTION_RECAPTCHA_USER  => self::OPTION_RECAPTCHA_USER_GUEST,
		self::OPTION_RECAPTCHA_BADGE => self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT,
	];


	/**
	 * AnyCommentAdminPages constructor.
	 *
	 * @param bool $init if required to init the modle.
	 */
	public function __construct( $init = true ) {
		parent::__construct();

		if ( $init ) {
			$this->init_hooks();
		}
	}

	/**
	 * Initiate hooks.
	 */
	private function init_hooks() {
		add_action( 'admin_init', [ $this, 'init_options' ] );
	}

	/**
	 * Init options.
	 * @return bool False on failure.
	 */
	public function init_options() {

		$form = $this->form();

		if ( is_plugin_active( 'akismet/akismet.php' ) ) {
			$form->add_section(
				$this->section_builder()
				     ->set_id( 'akismet' )
				     ->set_title( __( 'Akismet Anti-Spam', "anycomment" ) )
				     ->set_wrapper( '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab current" id="{id}">{content}</div>' )
				     ->set_fields( [
					     $this->field_builder()
					          ->set_id( self::OPTION_AKISMET )
					          ->checkbox()
					          ->set_title( __( 'Filter Spam via Akismet', "anycomment" ) )
					          ->set_description( sprintf( __( 'Filter all new comments through <a href="%s">Akismet Anti-Spam</a> plugin.', "anycomment" ), "https://wordpress.org/plugins/akismet/" ) ),
				     ] )
			);
		}

		if ( is_plugin_active( 'wp-user-avatar/wp-user-avatar.php' ) ) {
			$form->add_section(
				$this->section_builder()
				     ->set_id( 'wp_user_avatar' )
				     ->set_title( __( 'WP User Avatar', "anycomment" ) )
				     ->set_fields( [
					     $this->field_builder()
					          ->set_id( self::OPTION_WP_USER_AVATAR )
					          ->checkbox()
					          ->set_title( __( 'Force WP User Avatars', "anycomment" ) )
					          ->set_description( sprintf( __( 'Use <a href="%s">WP User Avatar</a> for handling avatars in the plugin.', "anycomment" ), "https://wordpress.org/plugins/wp-user-avatar/" ) ),
				     ] )
			);
		}

		$form->add_section(
			$this->section_builder()
			     ->set_id( 'recaptcha' )
			     ->set_title( __( 'reCAPTCHA', "anycomment" ) )
			     ->set_wrapper( '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab current" id="{id}">{content}</div>' )
			     ->set_description( function () {
				     $siteKey    = static::get_recaptcha_site_key();
				     $siteSecret = static::get_recaptcha_site_secret();
				     if ( static::is_recaptcha_active() && ( empty( $siteKey ) || empty( $siteSecret ) ) ) {
					     return '<p class="anycomment-notice anycomment-error">' . __( 'You have enabled reCAPTCHA, but did not specify fields: site key and site secret. These two fields are required.', 'anycomment' ) . '</p>';
				     }

				     return null;
			     } )
			     ->set_fields( [
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_RECAPTCHA_TOGGLE )
				          ->set_title( __( 'Enable', "anycomment" ) )
				          ->set_description( __( 'Enable reCAPTCHA. Make sure you have set API keys below as it will not work properly without them.' ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_RECAPTCHA_SITE_KEY )
				          ->set_title( __( 'Site key', "anycomment" ) )
				          ->set_description( sprintf( __( 'reCAPTCHA site key. Can be found <a href="%s">here</a> (register your website if does not exist). Please note that you should choose "Invisible" type in order for it to work.', "anycomment" ), "http://www.google.com/recaptcha/admin" ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_RECAPTCHA_SITE_SECRET )
				          ->set_title( __( 'Site secret', "anycomment" ) )
				          ->set_description( sprintf( __( 'reCAPTCHA site secret. Can be found <a href="%s">here</a> (register your website if does not exist). Please note that you should choose "Invisible" type in order for it to work.', "anycomment" ), "http://www.google.com/recaptcha/admin" ) ),

				     $this->field_builder()
				          ->select()
				          ->set_id( self::OPTION_RECAPTCHA_USER )
				          ->set_title( __( 'Users', 'anycomment' ) )
				          ->set_args( [
					          'options' => [
						          self::OPTION_RECAPTCHA_USER_ALL   => __( 'For all', 'anycomment' ),
						          self::OPTION_RECAPTCHA_USER_GUEST => __( 'For guests only', 'anycomment' ),
						          self::OPTION_RECAPTCHA_USER_AUTH  => __( 'For logged in only', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( __( 'Users affected by capcha.', "anycomment" ) ),

				     $this->field_builder()
				          ->select()
				          ->set_id( self::OPTION_RECAPTCHA_THEME )
				          ->set_title( __( 'Theme', 'anycomment' ) )
				          ->set_args( [
					          'options' => [
						          self::OPTION_RECAPTCHA_THEME_LIGHT => __( 'Light', 'anycomment' ),
						          self::OPTION_RECAPTCHA_THEME_DARK  => __( 'Dark', 'anycomment' ),
					          ],
				          ] ),

				     $this->field_builder()
				          ->select()
				          ->set_id( self::OPTION_RECAPTCHA_BADGE )
				          ->set_title( __( 'Position', 'anycomment' ) )
				          ->set_args( [
					          'options' => [
						          self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT  => __( 'Bottom right', 'anycomment' ),
						          self::OPTION_RECAPTCHA_BADGE_BOTTOM_LEFT   => __( 'Bottom left', 'anycomment' ),
						          self::OPTION_RECAPTCHA_BADGE_BOTTOM_INLINE => __( 'Inline', 'anycomment' ),
					          ]
				          ] )

			     ] )
		);

		return true;
	}

	/**
	 * Check whether Akismet filtration enabled or not.
	 *
	 * @return bool
	 */
	public static function is_akismet_active() {
		return static::instance()->get_db_option( self::OPTION_AKISMET ) !== null;
	}

	/**
	 * Check whether WP User Avatar is enabled or not.
	 *
	 * @since 0.0.3
	 * @return bool
	 */
	public static function is_wp_user_avatar_active() {
		return static::instance()->get_db_option( self::OPTION_WP_USER_AVATAR ) !== null;
	}

	/**
	 * Check whether reCAPTCHA is enabled or not.
	 *
	 * @since 0.0.56
	 * @return bool
	 */
	public static function is_recaptcha_active() {
		return static::instance()->get_db_option( self::OPTION_RECAPTCHA_TOGGLE ) !== null;
	}

	/**
	 * Check whether reCAPTCHA should be shown to all users.
	 *
	 * @since 0.0.56
	 * @return bool
	 */
	public static function is_recaptcha_user_all() {
		return static::get_recaptcha_user() === self::OPTION_RECAPTCHA_USER_ALL;
	}

	/**
	 * Check whether reCAPTCHA should be shown to guest users only.
	 *
	 * @since 0.0.56
	 * @return bool
	 */
	public static function is_recaptcha_user_guest() {
		return static::get_recaptcha_user() === self::OPTION_RECAPTCHA_USER_GUEST;
	}

	/**
	 * Check whether reCAPTCHA should be shown to logged in users only.
	 *
	 * @since 0.0.56
	 * @return bool
	 */
	public static function is_recaptcha_user_auth() {
		return static::get_recaptcha_user() === self::OPTION_RECAPTCHA_USER_AUTH;
	}

	/**
	 * Get reCAPTCHA user (for whom it will be shown).
	 *
	 * @since 0.0.56
	 * @return string|null
	 */
	public static function get_recaptcha_user() {
		$user = static::instance()->get_db_option( self::OPTION_RECAPTCHA_USER );

		if ( $user !== self::OPTION_RECAPTCHA_USER_ALL &&
		     $user !== self::OPTION_RECAPTCHA_USER_AUTH &&
		     $user !== self::OPTION_RECAPTCHA_USER_GUEST ) {
			return self::OPTION_RECAPTCHA_USER_GUEST;
		}

		return $user;
	}

	/**
	 * Get reCAPTCHA site key.
	 *
	 * @since 0.0.56
	 * @return string|null
	 */
	public static function get_recaptcha_site_key() {
		return static::instance()->get_db_option( self::OPTION_RECAPTCHA_SITE_KEY );
	}

	/**
	 * Get reCAPTCHA site secret.
	 *
	 * @since 0.0.56
	 * @return string|null
	 */
	public static function get_recaptcha_site_secret() {
		return static::instance()->get_db_option( self::OPTION_RECAPTCHA_SITE_SECRET );
	}

	/**
	 * Get reCAPTCHA theme.
	 *
	 * @since 0.0.56
	 * @return string|null
	 */
	public static function get_recaptcha_theme() {
		$theme = static::instance()->get_db_option( self::OPTION_RECAPTCHA_THEME );

		if ( $theme !== self::OPTION_RECAPTCHA_THEME_LIGHT && $theme !== self::OPTION_RECAPTCHA_THEME_DARK ) {
			return self::OPTION_RECAPTCHA_THEME_LIGHT;
		}

		return $theme;
	}

	/**
	 * Get reCAPTCHA badge (location of invisible captcha).
	 *
	 * @since 0.0.56
	 * @return string|null
	 */
	public static function get_recaptcha_badge() {
		$badge = static::instance()->get_db_option( self::OPTION_RECAPTCHA_BADGE );


		if ( $badge !== self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT &&
		     $badge !== self::OPTION_RECAPTCHA_BADGE_BOTTOM_LEFT &&
		     $badge !== self::OPTION_RECAPTCHA_BADGE_BOTTOM_INLINE ) {
			return self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT;
		}

		return $badge;
	}

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		$sections_html = '<form action="" id="' . $this->get_page_slug() . '" method="post" class="anycomment-form" novalidate>';

		$sections_html .= '<input type="hidden" name="option_name" value="' . $this->option_name . '">';

		$options = $this->options;

		foreach ( $options as $option ) {
			$sections = $option->get_sections();

			if ( ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					$sections_html .= $section;
				}
			} else {
				$fields = $option->get_fields();
				foreach ( $fields as $field ) {
					$sections_html .= $field;
				}
			}
		}

		$sections_html .= '<input type="submit" name="submit" id="submit" class="button button-primary" value="' . __( 'Save', 'anycomment' ) . '">';

		$sections_html .= '</form>';

		$tabs = $this->do_tab_menu();

		$html = <<<EOT
<div class="anycomment-tabs grid-x grid-margin-x">
	<aside class="cell large-5 medium-5 small-12 anycomment-tabs__menu">
	    $tabs
	</aside>
	<div class="cell auto anycomment-tabs__container">$sections_html</div>
</div>
EOT;

		return $html;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function do_tab_menu() {
		$options = $this->get_options();

		$html = '';

		foreach ( $options as $option ) {
			$html .= '<ul>';

			$sections = $option->get_sections();

			foreach ( $sections as $key => $section ) {
				$section_id = $section->get_id();

				$liClasses = ( $key == 0 ? 'current' : '' );

				$html .= '<li class="' . $liClasses . '" data-tab="' . $section_id . '">
				<a href="#tab-' . $section_id . '">' . $section->get_title() . '</a>
				</li>';
			}
		}
		$html .= '</ul>';

		$html .= <<<EOT
        <script>
            jQuery('.anycomment-tabs__menu li').on('click', function () {
                var data = jQuery(this).attr('data-tab') || '';
                var tab_id = '#' + data;

                if (!data) {
                    return false;
                }

                jQuery('.anycomment-tabs__menu li').removeClass('current');
                jQuery('.anycomment-tabs__container__tab').removeClass('current');

                jQuery(this).addClass('current');
                jQuery(tab_id).addClass('current');

                return false;
            })
        </script>
EOT;

		return $html;
	}
}
