<?php
/**
 * Plugin Name: AnyComment
 * Plugin URI: https://anycomment.io
 * Description: AnyComment is an advanced commenting system for WordPress.
 * Version: 0.0.66
 * Author: Bologer
 * Author URI: http://bologer.ru
 * Requires at least: 4.4
 * Requires PHP: 5.4
 * Tested up to: 4.9
 * Text Domain: anycomment
 * Domain Path: /languages
 *
 * @package AnyComment
 * @author bologer
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyComment' ) ) :

	/**
	 * Main AnyComment Class.
	 *
	 */
	class AnyComment {
		/**
		 * AnyComment version.
		 *
		 * @var string
		 */
		public $version = '0.0.66';

		/**
		 * Instance of render class.
		 *
		 * @var null|AnyCommentRender
		 */
		public $render = null;

		/**
		 * @var null|AnyCommentSocialAuth
		 */
		public $auth = null;

		/**
		 * @var Stash\Pool
		 */
		public $cache;

		/**
		 * Generic class prefix for all plugin HTML elements.
		 * @var string
		 */
		public $classPrefix = 'anycomment-';

		/**
		 * @var null|AnyCommentAdminPages
		 */
		public $admin_pages = null;

		/**
		 * @var null|AnyCommentStatistics
		 */
		public $statistics = null;

		/**
		 * @var null|AnyCommentMigration
		 */
		public $migrations = null;

		/**
		 * Instance of AnyComment.
		 * @var null|AnyComment
		 */
		private static $_instance = null;

		/**
		 * AnyComment constructor.
		 */
		public function __construct() {
			$this->init();

			do_action( 'anycomment_loaded' );
		}

		/**
		 * Init method to invoke starting scripts.
		 */
		public function init() {
			$this->define_constants();
			$this->includes();
			$this->init_textdomain();
			$this->init_hooks();
		}

		/**
		 * Load locale.
		 */
		public function init_textdomain() {
			load_plugin_textdomain( "anycomment", false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Main AnyComment Instance.
		 *
		 * Ensures only one instance of AnyComment is loaded or can be loaded.
		 *
		 * @since 2.1
		 * @static
		 * @see AnyComment()
		 * @return AnyComment Instance of plugin.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Initiate hooks.
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, [ $this, 'activation' ] );
			register_uninstall_hook( __FILE__, sprintf( '%s::uninstall', get_called_class() ) );

			if ( version_compare( AnyCommentOptions::get_migration(), $this->version, '<' ) ) {
				( new AnyCommentMigrationManager() )->applyAll();
			}
		}

		/**
		 * Activation method.
		 */
		public function activation() {
			// Apply migrations
			( new AnyCommentMigrationManager() )->applyAll();
		}

		/**
		 * Uninstall method.
		 */
		public static function uninstall() {
			remove_role( AnyCommentGenericSettings::DEFAULT_ROLE_SOCIAL_SUBSCRIBER );

			( new AnyCommentMigrationManager() )->dropAll();
		}

		/**
		 * Define AnyComment Constants.
		 */
		private function define_constants() {
			$this->define( 'ANYCOMMENT_PLUGIN_FILE', __FILE__ );
			$this->define( 'ANYCOMMENT_LANG', __FILE__ );
			$this->define( 'ANYCOMMENT_ABSPATH', dirname( __FILE__ ) . '/' );
			$this->define( 'ANYCOMMENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'ANYCOMMENT_VERSION', $this->version );
			$this->define( 'ANYCOMMENT_DEBUG', false );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get Ajax URL.
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			require_once( ANYCOMMENT_ABSPATH . 'vendor/autoload.php' );

			// Helpers
			include_once( ANYCOMMENT_ABSPATH . 'includes/helpers/AnyCommentHelper.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/helpers/AnyCommentInputHelper.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/helpers/AnyCommentLinkHelper.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/helpers/AnyCommentManipulatorHelper.php' );

			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentOptions.php' );

			// Cache
			include_once( ANYCOMMENT_ABSPATH . 'includes/cache/AnyCommentCacheManager.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/cache/AnyCommentRestCacheManager.php' );


			// Rest
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestController.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestCommentMeta.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestComment.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestLikeMeta.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestLikes.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestUsers.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestDocuments.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestRate.php' );

			/**
			 * Generic
			 */
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentErrorHandler.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentUploadHandler.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentRender.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/ac-core-functions.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentSocialAuth.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentRating.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentLikes.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentEmailQueue.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentUploadedFiles.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentUser.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentUserMeta.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentAvatars.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentComments.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentCommentMeta.php' );

			/**
			 * Hooks
			 */
			include_once( ANYCOMMENT_ABSPATH . 'includes/hooks/AnyCommentCommentHooks.php' );

			/**
			 * Admin related
			 */
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentStatistics.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentAdminOptions.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentAdminPages.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentGenericSettings.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentSocialSettings.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentGenericSettings.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentIntegrationSettings.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentFiles.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentWPComments.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentProblemNotifier.php' );

			/**
			 * Cron tabs
			 */
			include_once( ANYCOMMENT_ABSPATH . 'includes/cron/AnyCommentEmailQueueCron.php' );

			/**
			 * Migration manager
			 */
			include_once( ANYCOMMENT_ABSPATH . 'includes/migrations/AnyCommentMigrationManager.php' );

			/**
			 * Some plugin already have such class.
			 */
			if ( ! class_exists( 'Hybridauth' ) ) {
				include_once( ANYCOMMENT_ABSPATH . 'includes/hybridauth/src/autoload.php' );
			}

			new AnyCommentRestComment();
			new AnyCommentRestLikes();
			new AnyCommentRestUsers();
			new AnyCommentRestDocuments();
			new AnyCommentRestRate();
			new AnyCommentWPComments();
			new AnyCommentAvatars();

			/**
			 * Hooks init.
			 */
			new AnyCommentCommentHooks();

			/**
			 * Cron tabs.
			 */
			new AnyCommentEmailQueueCron();

			$this->render      = new AnyCommentRender();
			$this->admin_pages = new AnyCommentAdminPages();


			$cacheDriver = new Stash\Driver\FileSystem( [
				'path' => ANYCOMMENT_ABSPATH . 'cache/'
			] );

			$this->cache = new Stash\Pool( $cacheDriver );

			$this->auth       = new AnyCommentSocialAuth();
			$this->statistics = new AnyCommentStatistics();
		}
	}
endif;

function AnyComment() {
	return AnyComment::instance();
}

AnyComment();