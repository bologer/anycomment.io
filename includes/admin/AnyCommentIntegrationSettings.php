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
		protected $default_options = [];


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
			add_action( 'admin_menu', [ $this, 'add_menu' ] );
			add_action( 'admin_init', [ $this, 'init_options' ] );
		}

		/**
		 * Init admin menu.
		 */
		public function add_menu() {
			add_submenu_page(
				'anycomment-dashboard',
				__( 'Integration', "anycomment" ),
				__( 'Integration', "anycomment" ),
				'manage_options',
				$this->page_slug,
				[ $this, 'page_html' ]
			);
		}

		/**
		 * Init options.
		 * @return bool False on failure.
		 */
		public function init_options() {
			add_settings_section(
				'section_integration',
				__( 'Generic', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Integration with other plugin and/or services. Integration options will automatically appear down below once you install one of the supported plugins.', "anycomment" ) ?></p>
					<?php
				},
				$this->page_slug
			);

			$integrations = [];


			if ( is_plugin_active( 'akismet/akismet.php' ) ) {
				$integrations[] = [
					'id'          => self::OPTION_AKISMET,
					'title'       => __( 'Akismet Anti-Spam', "anycomment" ),
					'callback'    => 'input_checkbox',
					'description' => sprintf( __( 'Filter all new comments through <a href="%s">Akismet Anti-Spam</a> plugin.', "anycomment" ), "https://wordpress.org/plugins/akismet/" )
				];
			}

			if ( is_plugin_active( 'wp-user-avatar/wp-user-avatar.php' ) ) {
				$integrations[] = [
					'id'          => self::OPTION_WP_USER_AVATAR,
					'title'       => __( 'WP User Avatar', "anycomment" ),
					'callback'    => 'input_checkbox',
					'description' => sprintf( __( 'Use <a href="%s">WP User Avatar</a> for handling avatars in the plugin.', "anycomment" ), "https://wordpress.org/plugins/wp-user-avatar/" )
				];
			}

			$this->render_fields(
				$this->page_slug,
				'section_integration',
				$integrations
			);

			return true;
		}

		/**
		 * Check whether Akismet filtration enabled or not.
		 *
		 * @return bool
		 */
		public static function isAkismetOn() {
			return static::instance()->getOption( self::OPTION_AKISMET ) !== null;
		}

		/**
		 * Check whether WP User Avatar is enabled or not.
		 *
         * @since 0.0.3
		 * @return bool
		 */
		public static function isWPUserAvatarOn() {
			return static::instance()->getOption( self::OPTION_WP_USER_AVATAR ) !== null;
		}
	}
endif;

