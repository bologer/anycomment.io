<?php

namespace AnyComment;


use AnyComment\Admin\AnyCommentGenericSettings;

use AnyComment\Migrations\AnyCommentMigrationManager;

use Stash\Driver\FileSystem;
use Stash\Pool;

/**
 * Main AnyComment Class.
 *
 */
class AnyCommentCore {
	/**
	 * AnyComment version.
	 *
	 * @var string
	 */
	public $version = '0.0.70';

	/**
	 * @var Pool
	 */
	public $cache;

	/**
	 * Instance of AnyComment.
	 * @var null|AnyCommentCore
	 */
	private static $_instance = null;

	/**
	 * AnyComment constructor.
	 */
	public function __construct() {
		$this->init();

		/**
		 * Fires after AnyComment was loaded.
		 *
		 * @since 0.0.3
		 */
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
	 * @return AnyCommentCore Instance of plugin.
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


		add_action( 'init', function () {
			if ( version_compare( AnyCommentOptions::get_migration(), $this->version, '<' ) ) {
				( new AnyCommentMigrationManager() )->applyAll();
			}
		} );
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

		defined( 'ANYCOMMENT_PLUGIN_FILE' ) or define( 'ANYCOMMENT_PLUGIN_FILE', __FILE__ );
		defined( 'ANYCOMMENT_LANG' ) or define( 'ANYCOMMENT_LANG', __FILE__ );
		defined( 'ANYCOMMENT_ABSPATH' ) or define( 'ANYCOMMENT_ABSPATH', dirname( __FILE__ ) . '/../' );
		defined( 'ANYCOMMENT_PLUGIN_BASENAME' ) or define( 'ANYCOMMENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		defined( 'ANYCOMMENT_VERSION' ) or define( 'ANYCOMMENT_VERSION', $this->version );
		defined( 'ANYCOMMENT_DEBUG' ) or define( 'ANYCOMMENT_DEBUG', false );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/../', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) . '../' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Some plugin already have such class.
		 */
		if ( ! class_exists( 'Hybridauth' ) ) {
			include_once( ANYCOMMENT_ABSPATH . 'includes/hybridauth/src/autoload.php' );
		}

		AnyCommentLoader::load();

		$cacheDriver = new FileSystem( [
			'path' => ANYCOMMENT_ABSPATH . 'cache/'
		] );

		$this->cache = new Pool( $cacheDriver );
	}
}