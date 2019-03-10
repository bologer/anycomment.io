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
	public $version = '0.0.86';

	/**
	 * @var Pool
	 */
	protected static $cache;

	/**
	 * @var \Freemius
	 */
	public $freemius;

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
	public function __construct () {
		$this->init();
	}

	/**
	 * Init method to invoke starting scripts.
	 */
	public function init () {
		$this->includes();
		$this->init_textdomain();
		$this->init_hooks();
	}

	/**
	 * Load locale.
	 */
	public function init_textdomain () {
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
	public static function instance () {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initiate hooks.
	 */
	private function init_hooks () {
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
	public function activation () {
		// Apply migrations
		( new AnyCommentMigrationManager() )->apply_all();
	}

	/**
	 * Uninstall method.
	 */
	public static function uninstall () {
		remove_role( AnyCommentGenericSettings::DEFAULT_ROLE_SOCIAL_SUBSCRIBER );

		( new AnyCommentMigrationManager() )->drop_all();
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url () {
		return untrailingslashit( plugins_url( '/', ANYCOMMENT_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path () {
		return untrailingslashit( plugin_dir_path( ANYCOMMENT_PLUGIN_FILE ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes () {
		AnyCommentLoader::load();

		$this->init_freemius();
	}

	/**
	 * Get instance of cache.
	 *
	 * @return Pool
	 */
	public static function cache () {

		if ( static::$cache !== null ) {
			return static::$cache;
		}

		$cache_path = ABSPATH . str_replace( '/', DIRECTORY_SEPARATOR, 'wp-content/uploads/cache/anycomment' );

		if ( ! @file_exists( $cache_path ) ) {
			@mkdir( $cache_path, 0755, true );
		}

		$cacheDriver = new FileSystem( [
			'path' => $cache_path,
		] );

		static::$cache = new Pool( $cacheDriver );

		return static::$cache;
	}

	public function init_freemius () {

		if ( $this->freemius !== null ) {
			return $this->freemius;
		}

		// Include Freemius SDK.
		require_once ANYCOMMENT_ABSPATH . '/freemius/start.php';

		$this->freemius = fs_dynamic_init( array(
			'id'                  => '2926',
			'slug'                => 'anycomment',
			'type'                => 'plugin',
			'public_key'          => 'pk_362c323f4de13a39f79eedc50082f',
			'is_premium'          => false,
			// If your plugin is a serviceware, set this option to false.
			'has_premium_version' => false,
			'has_addons'          => true,
			'has_paid_plans'      => false,
			'menu'                => array(
				'slug'    => 'anycomment-dashboard',
				'contact' => false,
				'support' => false,
			),
		) );

		fs_override_i18n( [
			'add-ons'          => __( 'Add-Ons', 'anycomment' ),
			'opt-in-connect'   => translate_with_gettext_context( 'Allow & Continue', 'verb', 'anycomment' ),
			'skip'             => translate_with_gettext_context( 'Skip', 'verb', 'anycomment' ),
			'what-permissions' => __( 'What permissions are being granted?', 'anycomment' ),
			'privacy-policy'   => __( 'Privacy Policy', 'anycomment' ),
			'tos'              => __( 'Terms of Service', 'anycomment' ),
		], 'anycomment' );


		$this->freemius->add_filter( 'is_submenu_visible', function ( $is_visible, $submenu_id ) {
			if ( $submenu_id === 'pricing' ) {
				$is_visible = false;
			}

			return $is_visible;
		}, 10, 2 );

		/**
		 * @link https://freemius.com/help/documentation/wordpress-sdk/opt-in-message/
		 */
		// Existing users
		$this->freemius->add_filter( 'connect_message_on_update', function (
			$message,
			$user_first_name,
			$product_title,
			$user_login,
			$site_link,
			$freemius_link
		) {
			return sprintf(
				__( 'Hey %1$s', 'anycomment' ) . ',<br>' .
				__( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'anycomment' ),
				$user_first_name,
				'<b>' . $product_title . '</b>',
				'<b>' . $user_login . '</b>',
				$site_link,
				$freemius_link
			);
		}, 10, 6 );

		// New users
		$this->freemius->add_filter( 'connect_message', function (
			$message,
			$user_first_name,
			$product_title,
			$user_login,
			$site_link,
			$freemius_link
		) {
			return sprintf(
				__( 'Hey %1$s', 'anycomment', 'anycomment' ) . ',<br>' .
				__( 'never miss an important update from AnyComment – opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with freemius.com.', 'anycomment' ),
				$user_first_name,
				'<b>' . $product_title . '</b>',
				'<b>' . $user_login . '</b>',
				$site_link,
				$freemius_link
			);
		}, 10, 6 );

		do_action( 'any_fs_loaded' );

		return $this->freemius;
	}
}
