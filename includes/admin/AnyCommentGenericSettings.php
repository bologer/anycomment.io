<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentGenericSettings' ) ) :
	/**
	 * AC_AdminSettingPage helps to process generic plugin settings.
	 */
	class AnyCommentGenericSettings extends AnyCommentAdminOptions {

		/**
		 * Theme chosen for comments.
		 */
		const OPTION_THEME = 'option_theme';

		/**
		 * Checkbox whether plugin is active or not. Can be used to set-up API keys, etc,
		 * before plugin is ready to be shown to users.
		 */
		const OPTION_PLUGIN_TOGGLE = 'option_plugin_toggle';

		/**
		 * Default user group on register.
		 */
		const OPTION_REGISTER_DEFAULT_GROUP = 'option_register_default_group';


		/**
		 * Number of comments displayed per page and on the page load.
		 */
		const OPTION_COUNT_PER_PAGE = 'option_comments_count_per_page';

		/**
		 * Link to the user agreement.
		 */
		const OPTION_USER_AGREEMENT_LINK = 'option_comments_user_agreement_link';

		/**
		 * Show/hide copyright.
		 */
		const OPTION_COPYRIGHT_TOGGLE = 'option_copyright_toggle';

		/**
		 * Load comments on scroll to it.
		 */
		const OPTION_LOAD_ON_SCROLL = 'options_load_on_scroll';

		/**
		 * Mark comments for moderation before they are added.
		 */
		const OPTION_MODERATE_FIRST = 'options_moderate_first';

		/**
		 * Show/hide profile URL on client mini social icon.
		 */
		const OPTION_SHOW_PROFILE_URL = 'options_show_profile_url';

		/**
		 * Dark theme.
		 */
		const THEME_DARK = 'dark';

		/**
		 * Light theme.
		 */
		const THEME_LIGHT = 'light';

		/**
		 * Normal subscriber (from WordPress)
		 */
		const DEFAULT_ROLE_SUBSCRIBER = 'subscriber';

		/**
		 * Custom social subscriber. Role introduced via this plugin.
		 */
		const DEFAULT_ROLE_SOCIAL_SUBSCRIBER = 'social_subscriber';

		/**
		 * @inheritdoc
		 */
		protected $option_group = 'anycomment-generic-group';
		/**
		 * @inheritdoc
		 */
		protected $option_name = 'anycomment-generic';
		/**
		 * @inheritdoc
		 */
		protected $page_slug = 'anycomment-settings';

		/**
		 * @inheritdoc
		 */
		protected $default_options = [
			self::OPTION_THEME            => self::THEME_LIGHT,
			self::OPTION_COPYRIGHT_TOGGLE => 'on',
			self::OPTION_COUNT_PER_PAGE   => 20
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
			add_action( 'admin_menu', [ $this, 'add_menu' ] );
			add_action( 'admin_init', [ $this, 'init_settings' ] );

			// Create role
			add_role(
				AnyCommentGenericSettings::DEFAULT_ROLE_SOCIAL_SUBSCRIBER,
				__( 'Social Network Subscriber', 'anycomment' ),
				[
					'read'         => true,
					'edit_posts'   => false,
					'delete_posts' => false,
				]
			);
		}

		/**
		 * Init admin menu.
		 */
		public function add_menu() {
			add_submenu_page(
				'anycomment-dashboard',
				__( 'Settings', "anycomment" ),
				__( 'Settings', "anycomment" ),
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
				'section_generic',
				__( 'Generic', "anycomment" ),
				function () {
					echo '<p>' . __( 'Generic settings.', "anycomment" ) . '</p>';
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_generic',
				[
					[
						'id'          => self::OPTION_PLUGIN_TOGGLE,
						'title'       => __( 'Enable Comments', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'When on, comments are visible. When off, default WordPress\' comments shown. This can be used to configure social networks on fresh installation.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_REGISTER_DEFAULT_GROUP,
						'title'       => __( 'Register User Group', "anycomment" ),
						'callback'    => 'input_select',
						'description' => esc_html( __( 'When users will authorize via plugin, they are being registered and be assigned with group selected above.', "anycomment" ) ),
						'args'        => [
							'options' => [
								self::DEFAULT_ROLE_SUBSCRIBER        => __( 'Subscriber', 'anycomment' ),
								self::DEFAULT_ROLE_SOCIAL_SUBSCRIBER => __( 'Social Network Subscriber', 'anycomment' ),
							]
						],
					],
					[
						'id'          => self::OPTION_COUNT_PER_PAGE,
						'title'       => __( 'Number of Comments Loaded', "anycomment" ),
						'callback'    => 'input_number',
						'description' => esc_html( __( 'Number of comments loaded on initial page load. For example, "20" will display 20 comments on the page and if you have 40 in total, at the very bottom you will see button to load more. Min 5, max as defined.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_THEME,
						'title'       => __( 'Theme', "anycomment" ),
						'callback'    => 'input_select',
						'args'        => [
							'options' => [
								self::THEME_DARK  => __( 'Dark', 'anycomment' ),
								self::THEME_LIGHT => __( 'Light', 'anycomment' ),
							]
						],
						'description' => esc_html( __( 'Choose comments theme.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_LOAD_ON_SCROLL,
						'title'       => __( 'Load on Scroll', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Load comments when user scrolls to it.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_MODERATE_FIRST,
						'title'       => __( 'Moderate First', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Comment are not shown until they are approved by moderator. Users with ability to moderate comments will be ignored by filter.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_SHOW_PROFILE_URL,
						'title'       => __( 'Show Profile URL', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Show link to user in the social media when available (name of the user will be clickable).', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_USER_AGREEMENT_LINK,
						'title'       => __( 'User Agreement Link', "anycomment" ),
						'callback'    => 'input_text',
						'description' => esc_html( __( 'Link to User Agreement, where described how your process users data once they authorize via social network and/or add new comment.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_COPYRIGHT_TOGGLE,
						'title'       => __( 'Thanks', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Show AnyComment\'s link in the footer of comments. Copyright helps to bring awareness of such plugin and bring people to allow us to understand that it is a wanted product and give more often updated.', "anycomment" ) )
					],
				]
			);
		}

		/**
		 * Check whether plugin is enabled or not.
		 *
		 * @return bool
		 */
		public static function isEnabled() {
			return static::instance()->getOption( self::OPTION_PLUGIN_TOGGLE ) !== null;
		}

		/**
		 * Check whether it is required to load comments on scroll.
		 *
		 * @return bool
		 */
		public static function isLoadOnScroll() {
			return static::instance()->getOption( self::OPTION_LOAD_ON_SCROLL ) !== null;
		}

		/**
		 * Check whether it is required to mark comments for moderation.
		 *
		 * @return bool
		 */
		public static function isModerateFirst() {
			return static::instance()->getOption( self::OPTION_MODERATE_FIRST ) !== null;
		}

		/**
		 * Check whether it is required to show social profile URL or not.
		 *
		 * @return bool
		 */
		public static function isShowProfileUrl() {
			return static::instance()->getOption( self::OPTION_SHOW_PROFILE_URL ) !== null;
		}

		/**
		 * Get default group for registered user.
		 *
		 * @return string
		 */
		public static function getRegisterDefaultGroup() {
			return static::instance()->getOption( self::OPTION_REGISTER_DEFAULT_GROUP );
		}

		/**
		 * Get user agreement link. Used when user is guest and be authorizing using social network.
		 *
		 * @return string|null
		 */
		public static function getUserAgreementLink() {
			return static::instance()->getOption( self::OPTION_USER_AGREEMENT_LINK );
		}

		/**
		 * Get comment loaded per page setting value.
		 *
		 * @return int
		 */
		public static function getPerPage() {
			$value = (int) static::instance()->getOption( self::OPTION_COUNT_PER_PAGE );

			if ( $value < 5 ) {
				$value = 5;
			}

			return $value;
		}

		/**
		 * Get currently chosen theme.
		 * When value store is not matching any of the existing
		 * themes -> returns `dark` as default.
		 *
		 * @return string|null
		 */
		public static function getTheme() {
			$value = static::instance()->getOption( self::OPTION_THEME );

			if ( $value === null || $value !== self::THEME_DARK && $value !== self::THEME_LIGHT ) {
				return self::THEME_LIGHT;
			}

			return $value;
		}

		/**
		 * Check whether copyright should on or not.
		 *
		 * @return bool
		 */
		public static function isCopyrightOn() {
			return static::instance()->getOption( self::OPTION_COPYRIGHT_TOGGLE ) !== null;
		}
	}
endif;

