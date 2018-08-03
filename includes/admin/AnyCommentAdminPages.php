<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentAdminPages' ) ) :
	/**
	 * AnyCommentAdminPages helps to process website authentication.
	 */
	class AnyCommentAdminPages {
		/**
		 * @var AnyCommentSocialSettings
		 */
		public $page_options_social;

		/**
		 * @var AnyCommentGenericSettings
		 */
		public $page_options_general;

		/**
		 * @var AnyCommentIntegrationSettings
		 */
		public $page_options_integration;

		/**
		 * AnyCommentAdminPages constructor.
		 */
		public function __construct() {
			$this->init_hooks();
			$this->init();
		}

		/**
		 * Include pages.
		 */
		private function init() {
			$this->page_options_social      = new AnyCommentSocialSettings();
			$this->page_options_general     = new AnyCommentGenericSettings();
			$this->page_options_integration = new AnyCommentIntegrationSettings();
		}

		/**
		 * Initiate hooks.
		 */
		private function init_hooks() {
			add_action( 'admin_menu', [ $this, 'add_menu' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_dashboard_scripts' ] );
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
			echo anycomment_get_template( 'admin/dashboard' );
		}

		/**
		 * Load dashboard styles & scripts.
		 */
		public function enqueue_dashboard_scripts() {

			$page = $_GET['page'];

			if ( strpos( $page, 'anycomment' ) === false ) {
				return;
			}

			if ( $page === 'anycomment-dashboard' && ! isset( $_GET['tab'] ) ) {
				wp_enqueue_script( 'anycomment-admin-chartjs', AnyComment()->plugin_url() . '/assets/js/Chart.min.js', [], AnyComment()->version );
			}

			wp_enqueue_style( 'anycomment-admin-styles', AnyComment()->plugin_url() . '/assets/css/admin.min.css', [], AnyComment()->version );
			wp_enqueue_style( 'anycomment-admin-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic' );
		}

		/**
		 * Get resent news.
		 *
		 * @param int $per_page
		 *
		 * @return false|array Array on success (list of posts), false on failure.
		 */
		public function get_news( $per_page = 5 ) {

			$cacheKey = 'anycomment-plugin-news';

			if ( $per_page < 5 ) {
				$per_page = 5;
			}

			if ( ( $news = AnyComment()->cache->get( $cacheKey ) ) !== null ) {
				return json_decode( $news, true );
			}

			$url     = 'https://anycomment.io/wp-json/wp/v2/posts';
			$options = [
				'method'  => 'GET',
				'timeout' => 10,
				'body'    => [
					'per_page'   => $per_page,
					'type'       => 'post',
					'status'     => 'publish',
					'categories' => 15,
				]
			];

			$response = wp_remote_get( $url, $options );

			if ( ! is_wp_error( $response ) ) {
				/**
				 * @var WP_Posts_List_Table
				 */
				$posts = isset( $response['body'] ) ? $response['body'] : null;

				if ( $posts !== null ) {

					AnyComment()->cache->set( $cacheKey, $posts, strtotime( '+1 day' ) );

					return json_decode( $posts, true );
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
endif;

