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
	public $version = '0.0.76';

	/**
	 * @var Pool
	 */
	public $cache;

	/**
	 * @var
	 */
	public $scss;

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
				( new AnyCommentMigrationManager() )->apply_all();
			}
		} );
	}

	/**
	 * Activation method.
	 */
	public function activation() {
		// Apply migrations
		( new AnyCommentMigrationManager() )->apply_all();
	}

	/**
	 * Uninstall method.
	 */
	public static function uninstall() {
		remove_role( AnyCommentGenericSettings::DEFAULT_ROLE_SOCIAL_SUBSCRIBER );

		( new AnyCommentMigrationManager() )->drop_all();
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', ANYCOMMENT_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( ANYCOMMENT_PLUGIN_FILE ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		AnyCommentLoader::load();

		$cacheDriver = new FileSystem( [
			'path' => ANYCOMMENT_ABSPATH . 'cache/'
		] );

		$this->cache = new Pool( $cacheDriver );
	}
}