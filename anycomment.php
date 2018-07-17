<?php
/**
 * Plugin Name: AnyComment
 * Plugin URI: https://anycomment.io
 * Description: AnyComment is an advanced commenting system for WordPress.
 * Version: 0.0.35
 * Author: Bologer
 * Author URI: http://bologer.ru
 * Requires at least: 4.4
 * Requires PHP: 5.4
 * Tested up to: 4.7
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
		public $version = '0.0.35';

		/**
		 * Instance of render class.
		 *
		 * @var null|AnyCommentRender
		 */
		public $render = null;

		/**
		 * @var null|AnyCommentRestComment
		 */
		public $rest = null;

		/**
		 * Instance of error handler to keep information about erors.
		 *
		 * @var null|AnyCommentErrorHandler
		 */
		public $errors = null;

		/**
		 * @var null|AnyCommentSocialAuth
		 */
		public $auth = null;

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
		 * Post ID when available.
		 * @var null|WP_Post
		 */
		public $currentPost = null;

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
			register_uninstall_hook( __FILE__, [ $this, 'uninstall' ] );

			if ( version_compare( AnyCommentOptions::getMigration(), $this->version, '<' ) ) {
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
		public function uninstall() {
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
		 * Returns generic class prefix that should be used on all HTML elements.
		 *
		 * @return string
		 */
		public function classPrefix() {
			return $this->classPrefix;
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentOptions.php' );

			// Rest
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestController.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestCommentMeta.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestComment.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestLikeMeta.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestLikes.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/rest/AnyCommentRestUsers.php' );

			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentErrorHandler.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentUploadHandler.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentRender.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/ac-core-functions.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentSocialAuth.php' );
			include_once( ANYCOMMENT_ABSPATH . 'includes/AnyCommentLikes.php' );

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
			include_once( ANYCOMMENT_ABSPATH . 'includes/admin/AnyCommentWPComments.php' );

			// Migration manager
			include_once( ANYCOMMENT_ABSPATH . 'includes/migrations/AnyCommentMigrationManager.php' );

			if ( ! class_exists( 'Hybridauth' ) ) {
				include_once( ANYCOMMENT_ABSPATH . 'includes/hybridauth/src/autoload.php' );
			}

			$this->rest = new AnyCommentRestComment();
			new AnyCommentRestLikes();
			new AnyCommentRestUsers();
			new AnyCommentWPComments();

			$this->errors      = new AnyCommentErrorHandler();
			$this->render      = new AnyCommentRender();
			$this->admin_pages = new AnyCommentAdminPages();
			$this->auth        = new AnyCommentSocialAuth();
			$this->statistics  = new AnyCommentStatistics();
		}

		/**
		 * Fail response.
		 *
		 * @param string $error Error for response.
		 *
		 * @return string JSON fail response.
		 */
		public function json_error( $error, $response = [] ) {
			return $this->json_response( false, $response, $error );
		}

		/**
		 * Success response.
		 *
		 * @param array $response Specify custom response params.
		 *
		 * @return string
		 */
		public function json_success( $response = [] ) {
			return $this->json_response( true, $response );
		}

		/**
		 * @param bool $success Whether response is success or not.
		 * @param array $response Specify custom response
		 * @param $error
		 *
		 * @return string JSON string.
		 */
		public function json_response( $success = true, $response = [], $error = null ) {
			return json_encode( [
				'success'  => (bool) $success,
				'response' => json_encode( $response ),
				'error'    => $error,
				'time'     => time()
			] );
		}

		/**
		 * Set post object by having post.
		 *
		 * @param int $postId
		 */
		public function setCurrentPost( $postId = null ) {
			if ( $postId === null ) {
				$this->currentPost = get_post();
			} else {
				if ( ( $post = get_post( $postId ) ) !== null ) {
					$this->currentPost = $post;
				}
			}
		}

		/**
		 * Post when available.
		 * @return null|WP_Post
		 */
		public function getCurrentPost() {
			return $this->currentPost;
		}
	}
endif;

function AnyComment() {
	return AnyComment::instance();
}

AnyComment();