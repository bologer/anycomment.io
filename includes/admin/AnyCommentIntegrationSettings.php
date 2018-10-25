<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentIntegrationSettings' ) ) :
	/**
	 * AC_AdminSettingPage helps to process generic plugin settings.
	 */
	class AnyCommentIntegrationSettings extends AnyCommentAdminOptions {

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

			$integrations = [];

			if ( is_plugin_active( 'akismet/akismet.php' ) ) {
				$integrations[] = [
					'id'          => self::OPTION_AKISMET,
					'title'       => __( 'Akismet Anti-Spam', "anycomment" ),
					'type'        => 'checkbox',
					'description' => sprintf( __( 'Filter all new comments through <a href="%s">Akismet Anti-Spam</a> plugin.', "anycomment" ), "https://wordpress.org/plugins/akismet/" )
				];
			}

			if ( is_plugin_active( 'wp-user-avatar/wp-user-avatar.php' ) ) {
				$integrations[] = [
					'id'          => self::OPTION_WP_USER_AVATAR,
					'title'       => __( 'WP User Avatar', "anycomment" ),
					'type'        => 'checkbox',
					'description' => sprintf( __( 'Use <a href="%s">WP User Avatar</a> for handling avatars in the plugin.', "anycomment" ), "https://wordpress.org/plugins/wp-user-avatar/" )
				];
			}

			/**
			 * reCaptcha
			 */
			$integrations[] = [
				'id'          => self::OPTION_RECAPTCHA_TOGGLE,
				'title'       => __( 'Enable reCAPTCHA', "anycomment" ),
				'type'        => 'checkbox',
				'description' => __( 'Enable reCAPTCHA', "anycomment" )
			];

			$integrations[] = [
				'id'          => self::OPTION_RECAPTCHA_USER,
				'title'       => __( 'reCAPTCHA Users', "anycomment" ),
				'type'        => 'select',
				'options'     => [
					self::OPTION_RECAPTCHA_USER_ALL   => __( 'For all', 'anycomment' ),
					self::OPTION_RECAPTCHA_USER_GUEST => __( 'For guests only', 'anycomment' ),
					self::OPTION_RECAPTCHA_USER_AUTH  => __( 'For logged in only', 'anycomment' ),
				],
				'description' => __( 'Users affected by reCAPTCHA', "anycomment" )
			];

			$integrations[] = [
				'id'          => self::OPTION_RECAPTCHA_SITE_KEY,
				'title'       => __( 'reCAPTCHA Site Key', "anycomment" ),
				'type'        => 'text',
				'description' => sprintf( __( 'ШтмшытreCAPTCHA site key. Can be found <a href="%s">here</a> (register your website if does not exist). Please note that you should choose "Invisible" type in order for it to work.', "anycomment" ), "http://www.google.com/recaptcha/admin" )
			];

			$integrations[] = [
				'id'          => self::OPTION_RECAPTCHA_SITE_SECRET,
				'title'       => __( 'reCAPTCHA Site Secret', "anycomment" ),
				'type'        => 'text',
				'description' => sprintf( __( 'reCAPTCHA site secret. Can be found <a href="%s">here</a> (register your website if does not exist). Please note that you should choose "Invisible" type in order for it to work.', "anycomment" ), "http://www.google.com/recaptcha/admin" )
			];

			$integrations[] = [
				'id'          => self::OPTION_RECAPTCHA_THEME,
				'title'       => __( 'reCAPTCHA Theme', "anycomment" ),
				'type'        => 'select',
				'options'     => [
					self::OPTION_RECAPTCHA_THEME_LIGHT => __( 'Light', 'anycomment' ),
					self::OPTION_RECAPTCHA_THEME_DARK  => __( 'Dark', 'anycomment' ),
				],
				'description' => __( 'Theme of reCAPTCHA', "anycomment" )
			];

			$integrations[] = [
				'id'          => self::OPTION_RECAPTCHA_BADGE,
				'title'       => __( 'reCAPTCHA Position', "anycomment" ),
				'type'        => 'select',
				'args'        => [
					'options' => [
						self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT  => __( 'Bottom right', 'anycomment' ),
						self::OPTION_RECAPTCHA_BADGE_BOTTOM_LEFT   => __( 'Bottom left', 'anycomment' ),
						self::OPTION_RECAPTCHA_BADGE_BOTTOM_INLINE => __( 'Inline', 'anycomment' ),
					]

				],
				'description' => __( 'Position of reCAPTCHA', "anycomment" )
			];


			$this->render_fields(
				[
					'id'       => 'section_integration',
					'name'     => __( 'Generic', "anycomment" ),
					'callback' => function () {
						?>
                        <p><?php echo __( 'Integration with other plugin and/or services. Integration options will automatically appear down below once you install one of the supported plugins.', "anycomment" ) ?></p>
						<?php
					},
				],
				$integrations
			);

			return true;
		}

		/**
		 * Check whether Akismet filtration enabled or not.
		 *
		 * @return bool
		 */
		public static function is_akismet_active() {
			return static::instance()->get_option( self::OPTION_AKISMET ) !== null;
		}

		/**
		 * Check whether WP User Avatar is enabled or not.
		 *
		 * @since 0.0.3
		 * @return bool
		 */
		public static function is_wp_user_avatar_active() {
			return static::instance()->get_option( self::OPTION_WP_USER_AVATAR ) !== null;
		}

		/**
		 * Check whether reCAPTCHA is enabled or not.
		 *
		 * @since 0.0.56
		 * @return bool
		 */
		public static function is_recaptcha_active() {
			return static::instance()->get_option( self::OPTION_RECAPTCHA_TOGGLE ) !== null;
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
			$user = static::instance()->get_option( self::OPTION_RECAPTCHA_USER );

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
			return static::instance()->get_option( self::OPTION_RECAPTCHA_SITE_KEY );
		}

		/**
		 * Get reCAPTCHA site secret.
		 *
		 * @since 0.0.56
		 * @return string|null
		 */
		public static function get_recaptcha_site_secret() {
			return static::instance()->get_option( self::OPTION_RECAPTCHA_SITE_SECRET );
		}

		/**
		 * Get reCAPTCHA theme.
		 *
		 * @since 0.0.56
		 * @return string|null
		 */
		public static function get_recaptcha_theme() {
			$theme = static::instance()->get_option( self::OPTION_RECAPTCHA_THEME );

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
			$badge = static::instance()->get_option( self::OPTION_RECAPTCHA_BADGE );


			if ( $badge !== self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT &&
			     $badge !== self::OPTION_RECAPTCHA_BADGE_BOTTOM_LEFT &&
			     $badge !== self::OPTION_RECAPTCHA_BADGE_BOTTOM_INLINE ) {
				return self::OPTION_RECAPTCHA_BADGE_BOTTOM_RIGHT;
			}

			return $badge;
		}
	}
endif;

