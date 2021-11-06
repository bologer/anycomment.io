<?php

namespace AnyComment\Admin;

use AnyComment\AnyCommentCore;
use AnyComment\Base\BaseObject;
use AnyComment\Helpers\AnyCommentTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * AnyCommentAdminPages helps to process website authentication.
 */
class AnyCommentAdminPages extends BaseObject {
	/**
	 * @var AnyCommentSocialSettings
	 */
	private static $_page_socials;

	/**
	 * @var AnyCommentGenericSettings
	 */
	private static $_page_generic;

	/**
	 * @var AnyCommentIntegrationSettings
	 */
	private static $_page_integrations;

	/**
	 * Include pages.
	 */
	public function init() {
		static::$_page_socials      = new AnyCommentSocialSettings();
		static::$_page_generic      = new AnyCommentGenericSettings();
		static::$_page_integrations = new AnyCommentIntegrationSettings();

		$this->init_hooks();
	}

	/**
	 * Initiate hooks.
	 */
	private function init_hooks() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_head', [ $this, 'add_menu_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_dashboard_scripts' ] );
	}

	/**
	 * Add styles for menu.
	 * Primary used for fixing icon size
	 * and the way it displays.
	 */
	public function add_menu_styles() {
		?>
        <style>
            #toplevel_page_anycomment-dashboard .wp-menu-image img {
                height: 20px;
                width: auto;
                padding: 0;
                margin: 6px 0 0 3px;
            }
        </style>
		<?php
	}

	/**
	 * Init admin menu.
	 */
	public function add_menu() {
		add_menu_page(
			__( 'AnyComment', "anycomment" ),
			__( 'AnyComment', "anycomment" ),
			'manage_options',
			'anycomment-dashboard',
			[ $this, 'page_dashboard' ],
			AnyComment()->plugin_url() . '/assets/img/admin-menu-logo.png'
		);
	}

	/**
	 * Display dashboard page.
	 */
	public function page_dashboard() {
		echo AnyCommentTemplate::render( 'admin/dashboard' );
	}

	/**
	 * Load dashboard styles & scripts.
	 */
	public function enqueue_dashboard_scripts() {

		$page = isset( $_GET['page'] ) ? sanitize_text_field( trim( $_GET['page'] ) ) : null;

		if ( $page === null ) {
			return null;
		}

		if ( strpos( $page, 'anycomment' ) === false ) {
			return null;
		}

		if ( 0 === strpos( $page, 'anycomment' ) ) {
			wp_enqueue_script( 'anycomment-admin-chartjs', AnyComment()->plugin_url() . '/assets/js/Chart.min.js', [], AnyComment()->version );
		}

		wp_enqueue_script( 'anycomment-main-admin', AnyComment()->plugin_url() . '/assets/js/admin.min.js', [ 'jquery' ], AnyComment()->version, false );

		wp_enqueue_style( 'anycomment-admin-styles', AnyComment()->plugin_url() . '/assets/css/admin.min.css', [], AnyComment()->version );
		wp_enqueue_style( 'anycomment-admin-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic' );


		wp_localize_script( 'anycomment-main-admin', 'anycomment', [
			'tourNextLabel'     => esc_html__( 'Next', 'anycomment' ),
			'tourPreviousLabel' => esc_html__( 'Back', 'anycomment' ),
			'tourSkipLabel'     => esc_html__( 'Skip', 'anycomment' ),
			'tourDoneLabel'     => esc_html__( 'Hide', 'anycomment' ),
		] );
	}

	/**
	 * Get socials page class.
	 *
	 * @return AnyCommentSocialSettings
	 */
	public static function get_socials() {
		return static::$_page_socials;
	}

	/**
	 * Get generic page class.
	 *
	 * @return AnyCommentGenericSettings
	 */
	public static function get_generic() {
		return static::$_page_generic;
	}

	/**
	 * Get integrations page class.
	 *
	 * @return AnyCommentIntegrationSettings
	 */
	public static function get_integrations() {
		return static::$_page_integrations;
	}

	/**
	 * Get resent news.
	 *
	 * @param int $per_page
	 *
	 * @return false|array Array on success (list of posts), false on failure.
	 */
	public static function get_news( $per_page = 5 ) {

		$cacheKey   = sprintf( '/anycomment/news/%s/%s/%s', AnyComment()->version, get_locale(), $per_page );
		$cachedNews = AnyCommentCore::cache()->getItem( $cacheKey );

		$cachedJson = $cachedNews->get();

		if ( $cachedNews->isHit() ) {
			return json_decode( $cachedJson, true );
		}

		$locale = get_locale();

		// English
		$category = 24;

		if ( strpos( $locale, 'ru' ) !== false ) {
			$category = 15;
		}

		$shortLocale = substr( $locale, 0, 2 );


		$url = sprintf( 'https://plugin.anycomment.io/%swp-json/wp/v2/posts', strpos( $locale, 'en' ) !== false ? $shortLocale . '/' : '' );

		$options = [
			'method'  => 'GET',
			'timeout' => 10,
			'body'    => [
				'per_page'   => $per_page,
				'type'       => 'post',
				'status'     => 'publish',
				'categories' => $category,
			]
		];

		$response = wp_remote_get( $url, $options );

		if ( ! is_wp_error( $response ) ) {
			/**
			 * @var \WP_Posts_List_Table
			 */
			$posts = isset( $response['body'] ) ? $response['body'] : null;

			if ( $posts !== null ) {

				$cachedNews->set( $posts )->expiresAfter( strtotime( '+1 day' ) )->save();

				return json_decode( $posts, true );
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
