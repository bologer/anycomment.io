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
		 * Notify about new comment.
		 */
		const OPTION_NOTIFY_ON_NEW_COMMENT = 'option_notify_on_new_comment';

		/**
		 * Send email notification to users about new reply.
		 */
		const OPTION_NOTIFY_ON_NEW_REPLY = 'option_notify_on_new_reply';

		/**
		 * Notify administrator about new comment.
		 */
		const OPTION_NOTIFY_ADMINISTRATOR = 'option_notify_administrator';

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
		 * Interval, expressed in seconds per which check new comments.
		 * When OPTION_NOTIFY_ON_NEW_COMMENT is not enabled, this constant not used.
		 */
		const OPTION_INTERVAL_COMMENTS_CHECK = 'option_interval_comment_check';

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
		 * List of words to mark comments as spam.
		 */
		const OPTION_MODERATE_WORDS = 'options_moderate_words';

		/**
		 * Show/hide profile URL on client mini social icon.
		 */
		const OPTION_SHOW_PROFILE_URL = 'options_show_profile_url';

		/**
		 * Define form type: only guest users, only social networks or both of it.
		 */
		const OPTION_FORM_TYPE = 'options_form_type';

		/**
		 * FORM TYPES
		 */

		/**
		 * Option to enable comments only from guest.
		 */
		const FORM_OPTION_GUEST_ONLY = 'form_option_guest_only';

		/**
		 * Option to allow comments from users who authorized using social.
		 */
		const FORM_OPTION_SOCIALS_ONLY = 'form_option_socials_only';

		/**
		 * Option to allow both: guest & social login.
		 */
		const FORM_OPTION_ALL = 'form_option_all';

		/**
		 * THEMES
		 */

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
			self::OPTION_THEME                   => self::THEME_LIGHT,
			self::OPTION_COPYRIGHT_TOGGLE        => 'on',
			self::OPTION_COUNT_PER_PAGE          => 20,
			self::OPTION_INTERVAL_COMMENTS_CHECK => 10,
			self::OPTION_FORM_TYPE               => self::FORM_OPTION_SOCIALS_ONLY
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
						'id'          => self::OPTION_FORM_TYPE,
						'title'       => __( 'Comment form', "anycomment" ),
						'callback'    => 'input_select',
						'description' => esc_html( __( 'Comment form', "anycomment" ) ),
						'args'        => [
							'options' => [
								self::FORM_OPTION_ALL          => __( 'Social, WordPress & guests', 'anycomment' ),
								self::FORM_OPTION_SOCIALS_ONLY => __( 'Socials & WordPress users only.', 'anycomment' ),
								self::FORM_OPTION_GUEST_ONLY   => __( 'Guests only. ', 'anycomment' ),
							]
						],
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
						'description' => esc_html( __( 'Number of comments to load initially and per page.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_NOTIFY_ON_NEW_COMMENT,
						'title'       => __( 'New Comment Alert', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Show hint about new comment when user is on the comments page. Once clicked on alert, new comment will be displayed.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_INTERVAL_COMMENTS_CHECK,
						'title'       => __( 'New Comment Interval Checking', "anycomment" ),
						'callback'    => 'input_number',
						'description' => esc_html( __( 'Interval (in seconds) to check for new comments. Minimum 5 and maximum is 100 seconds.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_NOTIFY_ADMINISTRATOR,
						'title'       => __( 'Notify Administrator', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Notify administrator via email about new comment.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_NOTIFY_ON_NEW_REPLY,
						'title'       => __( 'Email Notifications', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Notify users by email (if specified) about new replies. Make sure you have proper SMTP configurations in order to send emails.', "anycomment" ) )
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
						'description' => esc_html( __( 'Moderators should check comment before it appears.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_MODERATE_WORDS,
						'title'       => __( 'Spam Words', "anycomment" ),
						'callback'    => 'input_textarea',
						'description' => esc_html( __( 'Comment should be marked for moderation when matched word from this list of comma-separated values.', "anycomment" ) )
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
		 * Check whether it is required to notify with alert on new comment.
		 *
		 * @return bool
		 */
		public static function isNotifyOnNewComment() {
			return static::instance()->getOption( self::OPTION_NOTIFY_ON_NEW_COMMENT ) !== null;
		}

		/**
		 * Check whether it is required to notify administrator about new comment.
		 *
		 * @return bool
		 */
		public static function isNotifyAdministrator() {
			return static::instance()->getOption( self::OPTION_NOTIFY_ADMINISTRATOR ) !== null;
		}

		/**
		 * Check whether it is required to notify by sending email on new reply.
		 *
		 * @return bool
		 */
		public static function isNotifyOnNewReply() {
			return static::instance()->getOption( self::OPTION_NOTIFY_ON_NEW_REPLY ) !== null;
		}

		/**
		 * Get list of words to moderate.
		 *
		 * @return string|null
		 */
		public static function getModerateWords() {
			return static::instance()->getOption( self::OPTION_MODERATE_WORDS );
		}

		/**
		 * Get interval in seconds per each check for new comments.
		 *
		 * @see AnyCommentGenericSettings::isNotifyOnNewReply() for more information. Which option is ignored when notification disabled.
		 *
		 * @return string
		 */
		public static function getIntervalCommentsCheck() {
			$intervalInSeconds = static::instance()->getOption( self::OPTION_INTERVAL_COMMENTS_CHECK );

			if ( $intervalInSeconds < 5 ) {
				$intervalInSeconds = 5;
			} elseif ( $intervalInSeconds > 100 ) {
				$intervalInSeconds = 100;
			}

			return $intervalInSeconds;
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
		 * Get form type.
		 *
		 * @return string|null
		 */
		public static function getFormType() {
			return static::instance()->getOption( self::OPTION_FORM_TYPE );
		}

		/**
		 * Check whether form type is for all.
		 *
		 * @return bool
		 */
		public static function isFormTypeAll() {
			return static::getFormType() === self::FORM_OPTION_ALL;
		}

		/**
		 * Check whether form type is for social only.
		 *
		 * @return bool
		 */
		public static function isFormTypeSocials() {
			return static::getFormType() === self::FORM_OPTION_SOCIALS_ONLY;
		}

		/**
		 * Check whether form type is for guests only.
		 *
		 * @return bool
		 */
		public static function isFormTypeGuests() {
			return static::getFormType() === self::FORM_OPTION_GUEST_ONLY;
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

