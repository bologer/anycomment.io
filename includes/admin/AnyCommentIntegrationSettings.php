<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentIntegrationSettings' ) ) :
	/**
	 * AC_AdminSettingPage helps to process generic plugin settings.
	 */
	class AnyCommentIntegrationSettings extends AnyCommentAdminOptions {
		const OPTION_AKISMET = 'option_akismet_toggle';

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
			add_action( 'admin_init', [ $this, 'init_settings' ] );
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
		 * {@inheritdoc}
		 */
		public function init_settings() {
			add_settings_section(
				'section_integration',
				__( 'Generic', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Integration with other plugin and/or services.', "anycomment" ) ?></p>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_integration',
				[
					[
						'id'          => self::OPTION_AKISMET,
						'title'       => __( 'Akismet Anti-Spam', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Filter all new comments through Akismet Anti-Spam plugin.', "anycomment" ) )
					],
				]
			);
		}

		/**
		 * Check whether Akismet filtration enabled or not.
		 *
		 * @return bool
		 */
		public static function isAkismetOn() {
			return static::instance()->getOption( self::OPTION_AKISMET ) !== null;
		}
	}
endif;

