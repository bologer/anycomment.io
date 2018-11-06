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
		 * Sender name used to send emails.
		 */
		const OPTION_NOTIFY_SENDER_NAME = 'option_notify_sender_name';

		/**
		 * Notify about new comment.
		 */
		const OPTION_NOTIFY_ON_NEW_COMMENT = 'option_notify_on_new_comment';

		/**
		 * Send email notification to users about new reply.
		 */
		const OPTION_NOTIFY_SUBSCRIBERS = 'option_notify_subscription';

		/**
		 * Send email notification to users about new reply.
		 */
		const OPTION_NOTIFY_ON_NEW_REPLY = 'option_notify_on_new_reply';

		/**
		 * Notify administrator about new comment.
		 */
		const OPTION_NOTIFY_ADMINISTRATOR = 'option_notify_administrator';

		/**
		 * Reply email template.
		 */
		const OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE = 'option_notify_reply_email_template';

		/**
		 * Reply email template.
		 */
		const OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE = 'option_notify_subscribe_email_template';

		/**
		 * Admin email template.
		 */
		const OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE = 'option_notify_admin_email_template';

		/**
		 * Checkbox whether plugin is active or not. Can be used to set-up API keys, etc,
		 * before plugin is ready to be shown to users.
		 */
		const OPTION_PLUGIN_TOGGLE = 'option_plugin_toggle';

		/**
		 * Default sort by.
		 */
		const OPTION_DEFAULT_SORT_BY = 'option_default_sort_by';


		/**
		 * Order ascending order.
		 */
		const SORT_ASC = 'asc';

		/**
		 * Order descending order.
		 */
		const SORT_DESC = 'desc';

		/**
		 * Default avatar.
		 */
		const OPTION_DEFAULT_AVATAR = 'option_default_avatar';

		const OPTION_DEFAULT_AVATAR_ANYCOMMENT = 'anycomment';
		const OPTION_DEFAULT_AVATAR_MP = 'mp';
		const OPTION_DEFAULT_AVATAR_IDENTICON = 'identicon';
		const OPTION_DEFAULT_AVATAR_MONSTEROID = 'monsterid';
		const OPTION_DEFAULT_AVATAR_WAVATAR = 'wavatar';
		const OPTION_DEFAULT_AVATAR_RETRO = 'retro';
		const OPTION_DEFAULT_AVATAR_ROBOHASH = 'robohash';

		/**
		 * Editor
		 */
		const OPTION_EDITOR_TOOLBAR_TOGGLE = 'option_editor_toggle';
		const OPTION_EDITOR_TOOLBAR_BOLD = 'option_editor_toolbar_bold';
		const OPTION_EDITOR_TOOLBAR_ITALIC = 'option_editor_toolbar_italic';
		const OPTION_EDITOR_TOOLBAR_UNDERLINE = 'option_editor_toolbar_underline';
		const OPTION_EDITOR_TOOLBAR_QUOTE = 'option_editor_toolbar_blockquote';
		const OPTION_EDITOR_TOOLBAR_ORDERED = 'option_editor_toolbar_ordered';
		const OPTION_EDITOR_TOOLBAR_BULLET = 'option_editor_toolbar_bullet';
		const OPTION_EDITOR_TOOLBAR_LINK = 'option_editor_toolbar_link';
		const OPTION_EDITOR_TOOLBAR_CLEAN = 'option_editor_toolbar_clean';


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
		 * General
		 */
		const OPTION_READ_MORE_TOGGLE = 'option_read_more_toggle'; // Ability to toggle read more.
		const OPTION_RATING_TOGGLE = 'option_rating_toggle'; // Display page rating.
		const OPTION_COUNT_PER_PAGE = 'option_comments_count_per_page'; // Number of comments displayed per page and on the page load.
		const OPTION_COMMENT_UPDATE_TIME = 'option_comment_update_time'; // Comment update time.
		const OPTION_USER_AGREEMENT_LINK = 'option_comments_user_agreement_link'; // Link to the user agreement.
		const OPTION_LOAD_ON_SCROLL = 'options_load_on_scroll'; // Load comments on scroll to it.
		const OPTION_MODERATE_FIRST = 'options_moderate_first'; // Mark comments for moderation before they are added.
		const OPTION_MODERATE_WORDS = 'options_moderate_words'; // List of words to mark comments as spam.
		const OPTION_LINKS_ON_HOLD = 'options_links_on_hold'; // Put comments with links on hold.
		const OPTION_SHOW_SOCIALS_IN_LOGIN_PAGE = 'options_show_socials_in_login_page'; // Show/hide list of available socials in WordPress's native login form.
		const OPTION_SHOW_ADMIN_BAR = 'options_show_admin_bar'; // Show/hide admin bar for authorized users (users with manage_options would be able to see it).
		const OPTION_SHOW_PROFILE_URL = 'options_show_profile_url'; // Show/hide profile URL on client mini social icon.
		const OPTION_SHOW_TWITTER_EMBEDS = 'options_show_tweet_attachments'; // Show tweet (from Twitter) attachments
		const OPTION_SHOW_VIDEO_ATTACHMENTS = 'options_show_video_attachments'; // Show/hide video attachments.
		const OPTION_SHOW_IMAGE_ATTACHMENTS = 'options_show_image_attachments'; // Show/hide image attachments.
		const OPTION_MAKE_LINKS_CLICKABLE = 'options_make_links_clickable'; // Whether required to make links clickable.
		const OPTION_COPYRIGHT_TOGGLE = 'option_copyright_toggle'; // Show/hide copyright.


		/**
		 * File upload
		 */
		const OPTION_FILES_TOGGLE = 'options_files_toggle';
		const OPTION_FILES_GUEST_CAN_UPLOAD = 'options_files_guest_can_upload';
		const OPTION_FILES_MIME_TYPES = 'options_files_mime_types';
		const OPTION_FILES_LIMIT = 'options_files_limit';
		const OPTION_FILES_LIMIT_PERIOD = 'options_files_limit_period';
		const OPTION_FILES_MAX_SIZE = 'options_files_max_size';

		/**
		 * Design
		 */
		const OPTION_FORM_TYPE = 'options_form_type'; // Define form type: only guest users, only social networks or both of it.
		const FORM_OPTION_GUEST_ONLY = 'form_option_guest_only'; // Option to enable comments only from guest.
		const FORM_OPTION_WORDPRESS_ONLY = 'form_option_wordpress_only'; // Option to allow comments from users who authorized using social.
		const FORM_OPTION_SOCIALS_ONLY = 'form_option_socials_only'; // Option to allow comments from users who authorized using social.
		const FORM_OPTION_ALL = 'form_option_all'; // Option to allow both: guest & social login.

		/**
		 * Define what fields to show and order.
		 */
		const OPTION_GUEST_FIELDS = 'options_guest_fields';

		/**
		 * Custom design options.
		 */
		const OPTION_DESIGN_CUSTOM_TOGGLE = 'options_design_custom_toggle';

		const OPTION_DESIGN_GLOBAL_PADDING = 'options_design_global_padding';
		const OPTION_DESIGN_GLOBAL_MARGIN = 'options_design_global_margin';
		const OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS = 'options_design_global_background_border_radius';
		const OPTION_DESIGN_GLOBAL_BACKGROUND_COLOR = 'options_design_global_background_color';

		const OPTION_DESIGN_FONT_SIZE = 'options_design_font_size';
		const OPTION_DESIGN_FONT_FAMILY = 'options_design_font_family';

		const OPTION_DESIGN_SEMI_HIDDEN_COLOR = 'options_design_semi_hidden_color';
		const OPTION_DESIGN_LINK_COLOR = 'options_design_link_color';
		const OPTION_DESIGN_TEXT_COLOR = 'options_design_text_color';

		const OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR = 'options_design_form_field_background_color';

		const OPTION_DESIGN_ATTACHMENT_COLOR = 'options_design_attachment_color';
		const OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR = 'options_design_attachment_background_color';

		const OPTION_DESIGN_AVATAR_RADIUS = 'options_design_avatar_radius';
		const OPTION_DESIGN_PARENT_AVATAR_SIZE = 'options_design_parent_avatar_size';
		const OPTION_DESIGN_CHILD_AVATAR_SIZE = 'options_design_child_avatar_size';

		const OPTION_DESIGN_BUTTON_COLOR = 'options_design_button_color';
		const OPTION_DESIGN_BUTTON_BACKGROUND_COLOR = 'options_design_button_background_color';
		const OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE = 'options_design_button_background_color_active';
		const OPTION_DESIGN_BUTTON_RADIUS = 'options_design_button_radius';

		const OPTION_DESIGN_GLOBAL_RADIUS = 'options_design_global_radius';

		/**
		 * Roles
		 */
		const DEFAULT_ROLE_SUBSCRIBER = 'subscriber'; // Normal subscriber (from WordPress)
		const DEFAULT_ROLE_SOCIAL_SUBSCRIBER = 'social_subscriber'; // Custom social subscriber. Role introduced via this plugin.


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
			self::OPTION_COPYRIGHT_TOGGLE        => 'on',
			self::OPTION_COMMENT_UPDATE_TIME     => 5,
			self::OPTION_COUNT_PER_PAGE          => 20,
			self::OPTION_INTERVAL_COMMENTS_CHECK => 10,

			self::OPTION_DEFAULT_SORT_BY                        => self::SORT_DESC,

			// Files
			self::OPTION_FILES_LIMIT                            => 5,
			self::OPTION_FILES_LIMIT_PERIOD                     => 900,
			self::OPTION_FILES_MAX_SIZE                         => 1.5,
			self::OPTION_FILES_MIME_TYPES                       => 'image/*, .pdf',

			// Notifications
			self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE      => "New comment in {blogUrlHtml}.\nFrom post {postUrlHtml}.\n\n{commentFormatted}\n{replyButton}\n\nYou were subscribed to this post.",
			self::OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE            => "New reply for you in {blogUrlHtml}.\nFrom post {postUrlHtml}.\n\n{commentFormatted}\n{replyButton}",
			self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE            => "New comment posted in {blogUrlHtml}.\nFor post {postUrlHtml}.\n\n{commentFormatted}\n{replyButton}",

			// Other design
			self::OPTION_FORM_TYPE                              => self::FORM_OPTION_SOCIALS_ONLY,
			self::OPTION_GUEST_FIELDS                           => '{name} {email} {website}',

			// Editor
			self::OPTION_EDITOR_TOOLBAR_TOGGLE                  => 'on',
			self::OPTION_EDITOR_TOOLBAR_BOLD                    => 'on',
			self::OPTION_EDITOR_TOOLBAR_ITALIC                  => 'on',
			self::OPTION_EDITOR_TOOLBAR_UNDERLINE               => 'on',
			self::OPTION_EDITOR_TOOLBAR_QUOTE                   => 'on',
			self::OPTION_EDITOR_TOOLBAR_ORDERED                 => 'on',
			self::OPTION_EDITOR_TOOLBAR_BULLET                  => 'on',
			self::OPTION_EDITOR_TOOLBAR_LINK                    => 'on',
			self::OPTION_EDITOR_TOOLBAR_CLEAN                   => 'on',

			// Custom design
			self::OPTION_DESIGN_GLOBAL_PADDING                  => '0',
			self::OPTION_DESIGN_GLOBAL_MARGIN                   => '20px 0',
			self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS => '0',
			self::OPTION_DESIGN_FONT_SIZE                       => '14px',
			self::OPTION_DESIGN_FONT_FAMILY                     => "'Noto-Sans', sans-serif",
			self::OPTION_DESIGN_SEMI_HIDDEN_COLOR               => '#B6C1C6',
			self::OPTION_DESIGN_LINK_COLOR                      => '#1DA1F2',
			self::OPTION_DESIGN_TEXT_COLOR                      => '#2A2E2E',
			self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR     => '#ffffff',
			self::OPTION_DESIGN_ATTACHMENT_COLOR                => '#eeeeee',
			self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR     => '#eeeeee',
			self::OPTION_DESIGN_AVATAR_RADIUS                   => '50% 50% 50% 0',
			self::OPTION_DESIGN_PARENT_AVATAR_SIZE              => '48px',
			self::OPTION_DESIGN_CHILD_AVATAR_SIZE               => '30px',
			self::OPTION_DESIGN_BUTTON_COLOR                    => '#ffffff',
			self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR         => '#1DA1F2',
			self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE  => '#4f9f49',
			self::OPTION_DESIGN_BUTTON_RADIUS                   => '20px',
			self::OPTION_DESIGN_GLOBAL_RADIUS                   => '10px',
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

			$this->render_fields(
				[
					'id'   => 'section_generic',
					'name' => __( 'Generic', "anycomment" ),
				],
				[
					[
						'id'          => self::OPTION_PLUGIN_TOGGLE,
						'title'       => __( 'Enable Comments', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'When on, comments are visible. When off, default WordPress\' comments shown. This can be used to configure social networks on fresh installation.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DEFAULT_SORT_BY,
						'title'       => __( 'Default Sorting', "anycomment" ),
						'type'        => 'select',
						'options'     => [
							self::SORT_DESC => __( 'Newest first', 'anycomment' ),
							self::SORT_ASC  => __( 'Oldest first', 'anycomment' ),
						],
						'description' => esc_html( __( 'Default sorting.', "anycomment" ) ),
					],
					[
						'id'          => self::OPTION_DEFAULT_AVATAR,
						'title'       => __( 'Default Avatar', "anycomment" ),
						'type'        => 'select',
						'options'     => [
							self::OPTION_DEFAULT_AVATAR_ANYCOMMENT => __( 'No avatar (from AnyComment)', 'anycomment' ),
							self::OPTION_DEFAULT_AVATAR_MP         => __( 'No avatar (from Gravatar)', 'anycomment' ),
							self::OPTION_DEFAULT_AVATAR_IDENTICON  => __( 'Identicon (from Gravatar)', 'anycomment' ),
							self::OPTION_DEFAULT_AVATAR_MONSTEROID => __( 'Monsteroid (from Gravatar)', 'anycomment' ),
							self::OPTION_DEFAULT_AVATAR_WAVATAR    => __( 'Wavatar (from Gravatar)', 'anycomment' ),
							self::OPTION_DEFAULT_AVATAR_RETRO      => __( 'Retro (from Gravatar)', 'anycomment' ),
							self::OPTION_DEFAULT_AVATAR_ROBOHASH   => __( 'Robohash (from Gravatar)', 'anycomment' ),
						],
						'description' => esc_html( __( 'Default avatar when user does not have any.', "anycomment" ) ),
					],
					[
						'id'          => self::OPTION_REGISTER_DEFAULT_GROUP,
						'title'       => __( 'Register User Group', "anycomment" ),
						'description' => esc_html( __( 'When users will authorize via plugin, they are being registered and be assigned with group selected above.', "anycomment" ) ),
						'type'        => 'select',
						'options'     => [
							self::DEFAULT_ROLE_SUBSCRIBER        => __( 'Subscriber', 'anycomment' ),
							self::DEFAULT_ROLE_SOCIAL_SUBSCRIBER => __( 'Social Network Subscriber', 'anycomment' ),
						]
					],
					[
						'id'          => self::OPTION_COMMENT_UPDATE_TIME,
						'title'       => __( 'Comment Update Time', "anycomment" ),
						'type'        => 'number',
						'description' => esc_html( __( 'Number of minutes user can update his comment. "0" or empty for no limit.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_COUNT_PER_PAGE,
						'title'       => __( 'Number of Comments Loaded', "anycomment" ),
						'type'        => 'number',
						'description' => esc_html( __( 'Number of comments to load initially and per page.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_LOAD_ON_SCROLL,
						'title'       => __( 'Load on Scroll', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Load comments when user scrolls to it.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_SHOW_SOCIALS_IN_LOGIN_PAGE,
						'title'       => __( 'Show Login Page Socials', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show list of available socials under WordPress\'s native login form.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_SHOW_ADMIN_BAR,
						'title'       => __( 'Show Admin Bar', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show admin bar for regular WordPress users and those who logged in via social.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_SHOW_PROFILE_URL,
						'title'       => __( 'Show Profile URL', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show link to user in the social media or website when available (name of the user will be clickable).', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_SHOW_TWITTER_EMBEDS,
						'title'       => __( 'Display Twitter Embeds', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Detect & display tweets from Twitter as embedded widget.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_SHOW_VIDEO_ATTACHMENTS,
						'title'       => __( 'Display Video Attachments', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Display video link from comment as attachment.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_SHOW_IMAGE_ATTACHMENTS,
						'title'       => __( 'Display Image Attachments', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Display image link from comment as attachment.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_RATING_TOGGLE,
						'title'       => __( 'Display Rating', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Display 5 star rating above comments.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_READ_MORE_TOGGLE,
						'title'       => __( 'Shorten Long Comments', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Shorten long comments with "Read more" message.', "anycomment" ) )
					],

//					[
//						'id'          => self::OPTION_MAKE_LINKS_CLICKABLE,
//						'title'       => __( 'Links Clickable', "anycomment" ),
//						'callback'    => 'input_checkbox',
//						'description' => esc_html( __( 'Links in comment are clickable.', "anycomment" ) )
//					],

					[
						'id'          => self::OPTION_USER_AGREEMENT_LINK,
						'title'       => __( 'User Agreement Link', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Link to User Agreement, where described how your process users data once they authorize via social network and/or add new comment.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_COPYRIGHT_TOGGLE,
						'title'       => __( 'Thanks', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show AnyComment\'s link in the footer of comments. Copyright helps to bring awareness of such plugin and bring people to allow us to understand that it is a wanted product and give more often updated.', "anycomment" ) )
					],
				]
			);

			$this->render_fields(
				[
					'id'   => 'section_elements',
					'name' => __( 'Elements', "anycomment" ),
				],
				[
					[
						'id'          => self::OPTION_FORM_TYPE,
						'title'       => __( 'Comment form', "anycomment" ),
						'type'        => 'select',
						'options'     => [
							self::FORM_OPTION_ALL            => __( 'Anyone', 'anycomment' ),
							self::FORM_OPTION_WORDPRESS_ONLY => __( 'WordPress only', 'anycomment' ),
							self::FORM_OPTION_SOCIALS_ONLY   => __( 'Socials only', 'anycomment' ),
							self::FORM_OPTION_GUEST_ONLY     => __( 'Guests only', 'anycomment' ),
						],
						'description' => esc_html( __( 'Users who able to leave comments.', "anycomment" ) ),
					],
					[
						'id'          => self::OPTION_GUEST_FIELDS,
						'title'       => __( 'Guest Fields', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Use this rearrange guest form fields or remove something. {name} is required and if you do not add it, it will be added by plugin. {name} is name field, {email} is email field, {website} is website field.', "anycomment" ) )
					],


					/**
					 * Editor options
					 */
					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_TOGGLE,
						'title'       => __( 'Enable Toolbar', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Enable editor toolbar (show options to modify comment text - bold, italics, etc).', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_BOLD,
						'title'       => __( 'Bold', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show bold option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_ITALIC,
						'title'       => __( 'Italic', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show italic option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_UNDERLINE,
						'title'       => __( 'Underline', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show underline option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_QUOTE,
						'title'       => __( 'Quote', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show quote option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_ORDERED,
						'title'       => __( 'Ordered list', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show ordered list option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_BULLET,
						'title'       => __( 'Unordered list', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show unordered list option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_LINK,
						'title'       => __( 'Link', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show link option in editor toolbar.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_EDITOR_TOOLBAR_CLEAN,
						'title'       => __( 'Clean formatting', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show clean formatting option in editor toolbar.', "anycomment" ) )
					],
				]
			);

			$this->render_fields(
				[
					'id'   => 'section_design',
					'name' => __( 'Design', "anycomment" ),
				],
				[
					/**
					 * Custom design
					 */
					[
						'id'          => self::OPTION_DESIGN_CUSTOM_TOGGLE,
						'title'       => __( 'Custom Design', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Use custom design. Enable this option to display design changes from below.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_FONT_SIZE,
						'title'       => __( 'Text Size', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Overal text size. You may use "px", "pt", "em" or "%".', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_FONT_FAMILY,
						'title'       => __( 'Font Choice', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Global font family.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_TEXT_COLOR,
						'title'       => __( 'Text Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Global text color.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_LINK_COLOR,
						'title'       => __( 'Link Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Links color.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_GLOBAL_BACKGROUND_COLOR,
						'title'       => __( 'Global background color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Global background color used for all comments.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS,
						'title'       => __( 'Global background border radius', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Global background border radius. Could be useful when you have background different then website primary color.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_GLOBAL_MARGIN,
						'title'       => __( 'Global margin', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Global margin for all comments. You may use "px", "em" or "%".', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_GLOBAL_PADDING,
						'title'       => __( 'Global padding', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Global padding for all comments. You may use "px", "em" or "%".', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_GLOBAL_RADIUS,
						'title'       => __( 'Border radius', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Border radius. You may use "px", "em" or "%".', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_SEMI_HIDDEN_COLOR,
						'title'       => __( 'Semi Hidden Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Semi hidden color. This is used for dates, action links, etc.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR,
						'title'       => __( 'Form Fields Background', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Form fields background color.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_ATTACHMENT_COLOR,
						'title'       => __( 'Attachment Text Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Attachments text color. For example, YouTube attachments do not have previews, instead they have "YouTube" text over.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR,
						'title'       => __( 'Attachment Background Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Attachment background color. For example, user may attach PNG image with transparent background. This color will be used as background behind the image.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_AVATAR_RADIUS,
						'title'       => __( 'Avatar Border Radius', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Avatar border radius. You may use "px" or "%". "50%" will make avatars rounded.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_PARENT_AVATAR_SIZE,
						'title'       => __( 'Avatar Parent Size', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Parent comment avatar size.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_CHILD_AVATAR_SIZE,
						'title'       => __( 'Avatar Child Size', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Child comment avatar size. Usually, this is reply comment.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_BUTTON_RADIUS,
						'title'       => __( 'Button Radius', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Button border radius.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_BUTTON_COLOR,
						'title'       => __( 'Button Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Button text color.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR,
						'title'       => __( 'Button Background Color', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Button background color.', "anycomment" ) )
					],

					[
						'id'          => self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE,
						'title'       => __( 'Button Background Color Active', "anycomment" ),
						'type'        => 'color',
						'description' => esc_html( __( 'Button background color when hovered or focused.', "anycomment" ) )
					],
				]
			);

			$this->render_fields(
				[
					'id'   => 'section_moderation',
					'name' => __( 'Moderation', "anycomment" ),
				],
				[
					[
						'id'          => self::OPTION_MODERATE_FIRST,
						'title'       => __( 'Moderate First', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Moderators should check comment before it appears.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_LINKS_ON_HOLD,
						'title'       => __( 'Links on Hold', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Comment with links should be marked for moderation.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_MODERATE_WORDS,
						'title'       => __( 'Spam Words', "anycomment" ),
						'type'        => 'textarea',
						'description' => esc_html( __( 'Comment should be marked for moderation when matched word from this list of comma-separated values.', "anycomment" ) )
					],
				]
			);

			$this->render_fields(
				[
					'id'   => 'section_notifications',
					'name' => __( 'Notifications', "anycomment" ),
				],
				[
					[
						'id'          => self::OPTION_NOTIFY_SENDER_NAME,
						'title'       => __( 'Sender Name', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Send name shown to email recipient. This could be your blog name.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_NOTIFY_ON_NEW_COMMENT,
						'title'       => __( 'New Comment Alert', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show alert about new comment when user is on the page.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_INTERVAL_COMMENTS_CHECK,
						'title'       => __( 'New Comment Interval Checking', "anycomment" ),
						'type'        => 'number',
						'description' => esc_html( __( 'Interval (in seconds) to check for new comments. Minimum 5 and maximum is 100 seconds.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_NOTIFY_ADMINISTRATOR,
						'title'       => __( 'Notify Administrator', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Notify administrator via email about new comment.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_NOTIFY_ON_NEW_REPLY,
						'title'       => __( 'Notify on new replies', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Notify users by email (if specified) about new replies. Make sure you have proper SMTP configurations in order to send emails.', "anycomment" ) ),
					],

					[
						'id'          => self::OPTION_NOTIFY_SUBSCRIBERS,
						'title'       => __( 'Notify Post Subscribers', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Show subscription form and notify active post subscribers. Make sure you have proper SMTP configurations in order to send emails.', "anycomment" ) ),
					],

					[
						'id'          => self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE,
						'title'       => __( 'Subscription Email Template', "anycomment" ),
						'type'        => 'textarea',
						'description' => esc_html( __( 'Email template for subscriptions.', "anycomment" ) ),
						'after'       => function () {
							$supportedList = [
								'{blogName}'         => __( 'Blog name as text', 'anycomment' ),
								'{blogUrl}'          => __( 'Blog link as text', 'anycomment' ),
								'{blogUrlHtml}'      => __( 'Blog name in HTML link', 'anycomment' ),
								'{postTitle}'        => __( 'Post title as text', 'anycomment' ),
								'{postUrl}'          => __( 'Post URL as text', 'anycomment' ),
								'{postUrlHtml}'      => __( 'Post title in HTML link', 'anycomment' ),
								'{commentText}'      => __( 'Comment text', 'anycomment' ),
								'{commentFormatted}' => __( 'Comment text nicely formatted', 'anycomment' ),
								'{replyUrl}'         => __( 'Reply link as text', 'anycomment' ),
								'{replyButton}'      => __( 'Reply link as button', 'anycomment' ),
							];

							$id = self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE . time();

							$html = '<div><span class="button button-small" id="' . $id . '">' . __( 'More info', 'anycomment' ) . '</span><ul style="display: none;">';

							foreach ( $supportedList as $code => $description ) {
								$html .= sprintf( "<li>%s - %s</li>", $code, $description );
							}

							$html .= '</ul></div>';

							$html .= '<script>jQuery("#' . $id . '").on("click", function() {jQuery(this).next("ul").toggle();});</script>';

							return $html;
						}
					],

					[
						'id'          => self::OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE,
						'title'       => __( 'Reply Email Template', "anycomment" ),
						'type'        => 'textarea',
						'description' => esc_html( __( 'Email template on new comment reply.', "anycomment" ) ),
						'after'       => function () {
							$supportedList = [
								'{blogName}'         => __( 'Blog name as text', 'anycomment' ),
								'{blogUrl}'          => __( 'Blog link as text', 'anycomment' ),
								'{blogUrlHtml}'      => __( 'Blog name in HTML link', 'anycomment' ),
								'{postTitle}'        => __( 'Post title as text', 'anycomment' ),
								'{postUrl}'          => __( 'Post URL as text', 'anycomment' ),
								'{postUrlHtml}'      => __( 'Post title in HTML link', 'anycomment' ),
								'{commentText}'      => __( 'Comment text', 'anycomment' ),
								'{commentFormatted}' => __( 'Comment text nicely formatted', 'anycomment' ),
								'{replyUrl}'         => __( 'Reply link as text', 'anycomment' ),
								'{replyButton}'      => __( 'Reply link as button', 'anycomment' ),
							];

							$id = self::OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE . time();

							$html = '<div><span class="button button-small" id="' . $id . '">' . __( 'More info', 'anycomment' ) . '</span><ul style="display: none;">';

							foreach ( $supportedList as $code => $description ) {
								$html .= sprintf( "<li>%s - %s</li>", $code, $description );
							}

							$html .= '</ul></div>';

							$html .= '<script>jQuery("#' . $id . '").on("click", function() {jQuery(this).next("ul").toggle();});</script>';

							return $html;
						}
					],

					[
						'id'          => self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE,
						'title'       => __( 'Admin Email Template', "anycomment" ),
						'type'        => 'textarea',
						'description' => esc_html( __( 'Email template sent to admin about new comment.', "anycomment" ) ),
						'after'       => function () {
							$supportedList = [
								'{blogName}'           => __( 'Blog name as text', 'anycomment' ),
								'{blogUrl}'            => __( 'Blog link as text', 'anycomment' ),
								'{blogUrlHtml}'        => __( 'Blog name in HTML link', 'anycomment' ),
								'{postTitle}'          => __( 'Post title as text', 'anycomment' ),
								'{postUrl}'            => __( 'Post URL as text', 'anycomment' ),
								'{postUrlHtml}'        => __( 'Post title in HTML link', 'anycomment' ),
								'{commentText}'        => __( 'Comment text', 'anycomment' ),
								'{commentFormatted}'   => __( 'Comment text nicely formatted', 'anycomment' ),
								'{replyUrl}'           => __( 'Reply link as text', 'anycomment' ),
								'{replyButton}'        => __( 'Reply link as button', 'anycomment' ),
								'{adminModerationUrl}' => __( 'Direct link to admin to see all comments waiting to reviewed', 'anycomment' ),
								'{adminEditUrl}'       => __( 'Direct link to admin to edit comment', 'anycomment' ),
							];

							$id = self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE . time();

							$html = '<div><br><span class="button button-small" id="' . $id . '">' . __( 'More info', 'anycomment' ) . '</span><ul style="display: none;">';

							foreach ( $supportedList as $code => $description ) {
								$html .= sprintf( "<li>%s - %s</li>", $code, $description );
							}

							$html .= '</ul></div>';

							$html .= '<script>jQuery("#' . $id . '").on("click", function() {jQuery(this).next("ul").toggle();});</script>';

							return $html;
						}
					],
				]
			);

			$this->render_fields(
				[
					'id'   => 'section_files',
					'name' => __( 'Files', "anycomment" ),
				],
				[
					[
						'id'          => self::OPTION_FILES_TOGGLE,
						'title'       => __( 'Allow File Uploads', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Allow to upload files.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_FILES_GUEST_CAN_UPLOAD,
						'title'       => __( 'File Upload By Guests', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Guest users can upload documents. Please be careful about this setting as some users may potentially misuse this and periodically upload unwanted files.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_FILES_MIME_TYPES,
						'title'       => __( 'File MIME Types', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Comman-separated list of allowed MIME types (e.g. .png, .jpg, etc). Alternatively, you may write "image/*" for all image types or "audio/*" for audios.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_FILES_LIMIT,
						'title'       => __( 'File Upload Limit', "anycomment" ),
						'type'        => 'number',
						'description' => esc_html( __( 'Maximum number of files to upload per period defined in the field below.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_FILES_LIMIT_PERIOD,
						'title'       => __( 'File Upload Limit Period', "anycomment" ),
						'type'        => 'number',
						'description' => esc_html( __( 'If user will cross the limit (defined above) within specified period (in seconds) in this field, he will be give a warning.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_FILES_MAX_SIZE,
						'title'       => __( 'File Size', "anycomment" ),
						'type'        => 'number',
						'description' => esc_html( __( 'Maximum allowed file size in megabytes. For example, regular PNG image is about ~ 1.5-2MB, JPEG are even smaller.', "anycomment" ) )
					],
				]
			);
		}

		/**
		 * top level menu:
		 * callback functions
		 *
		 * @param bool $wrapper Whether to wrap for with header or not.
		 */
		public function page_html( $wrapper = true ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET['settings-updated'] ) ) {
				add_settings_error( $this->alert_key, 'anycomment_message', __( 'Settings Saved', 'anycomment' ), 'updated' );
			}

			if ( AnyCommentGenericSettings::is_design_custom() ) {
				static::apply_style_on_design_change();
			}

			settings_errors( $this->alert_key );
			?>
			<?php if ( $wrapper ): ?>
                <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php endif; ?>
            <form action="options.php" method="post" class="anycomment-form" novalidate>
				<?php
				settings_fields( $this->option_group );
				?>

                <div class="anycomment-tabs grid-x grid-margin-x">
                    <aside class="cell large-4 medium-5 small-12 anycomment-tabs__menu">
						<?php $this->do_tab_menu( $this->page_slug ) ?>
                    </aside>
                    <div class="cell auto anycomment-tabs__container">
						<?php
						$this->do_tab_sections( $this->page_slug, false );
						submit_button( __( 'Save', 'anycomment' ) );
						?>
                    </div>
                </div>
            </form>
			<?php if ( $wrapper ): ?>
                </div>
			<?php endif; ?>
			<?php
		}

		/**
		 * Used for customized theme'ing.
		 *
		 * It can combine multiple SCSS to one SCSS, convert it to CSS, minify,
		 * replace images with static from react. About last point read URL below.
		 *
		 * @link https://github.com/matthiasmullie/minify can be added later for minifying result CSS for speed-up purposes
		 *
		 * @return string String on success, false on failure.
		 */
		private static function combine_styles_and_process() {
			$scssPath = AnyComment()->plugin_path() . '/assets/theming/';

			$content = trim( file_get_contents( $scssPath . 'app.scss' ) );

			if ( empty( $content ) ) {
				return false;
			}


			$toastCss = file_get_contents( $scssPath . 'ReactToastify.css' );

			if ( $toastCss !== false ) {
				$content .= $toastCss;
			}

			include_once( AnyComment()->plugin_path() . '/includes/libs/scssphp/scss.inc.php' );

			$scss = new \Leafo\ScssPhp\Compiler();
			$scss->setFormatter( 'Leafo\ScssPhp\Formatter\Crunched' );
			$scss->addImportPath( $scssPath );

			$replaceVariables = [
				'global-margin'                   => AnyCommentGenericSettings::get_global_margin(),
				'global-padding'                  => AnyCommentGenericSettings::get_global_padding(),
				'global-background-color'         => AnyCommentGenericSettings::get_global_background_color(),
				'global-background-border-radius' => AnyCommentGenericSettings::get_global_background_border_radius(),

				'font-size'   => AnyCommentGenericSettings::get_design_font_size(),
				'font-family' => AnyCommentGenericSettings::get_design_font_family(),
				'link-color'  => AnyCommentGenericSettings::get_design_link_color(),
				'text-color'  => AnyCommentGenericSettings::get_design_text_color(),

				'semi-hidden-color' => AnyCommentGenericSettings::get_design_semi_hidden_color(),

				'form-field-background-color' => AnyCommentGenericSettings::get_design_form_field_background_color(),

				'attachment-color'            => AnyCommentGenericSettings::get_design_attachment_color(),
				'attachment-background-color' => AnyCommentGenericSettings::get_design_attachment_background_color(),

				'avatar-border-radius' => AnyCommentGenericSettings::get_design_avatar_radius(),
				'parent-avatar-size'   => AnyCommentGenericSettings::get_design_parent_avatar_size(),
				'child-avatar-size'    => AnyCommentGenericSettings::get_design_child_avatar_size(),

				'btn-radius'                  => AnyCommentGenericSettings::get_design_button_radius(),
				'btn-color'                   => AnyCommentGenericSettings::get_design_button_color(),
				'btn-background-color'        => AnyCommentGenericSettings::get_design_button_background_color(),
				'btn-background-color-active' => AnyCommentGenericSettings::get_design_button_background_color_active(),

				'global-radius' => AnyCommentGenericSettings::get_design_global_radius(),
			];

			$scss->setVariables( $replaceVariables );

			$compiled = $scss->compile( $content );

			/**
			 * Replace relative paths of the images in the stylesheet with react-way,
			 * as there is no way to remove it via react-create-app
			 * @link https://github.com/facebook/create-react-app/issues/821 for further information
			 */
			$staticFolder = AnyComment()->plugin_path() . '/static/media/';
			$assets       = $staticFolder . '*.*';

			$fileAssetList = glob( $assets );

			if ( ! empty( $fileAssetList ) ) {
				foreach ( $fileAssetList as $key => $assetFullPath ) {
					preg_match( '/\/media\/(.*)\.[a-z0-9]+\.(svg|png|jpg|jpeg|ico|gif)$/m', $assetFullPath, $matches );

					if ( count( $matches ) !== 3 ) {
						continue;
					}

					$fullMatchAndUrl = AnyComment()->plugin_url() . '/static' . $matches[0];
					$fileName        = $matches[1];
					$extension       = $matches[2];

					$pattern = "/\.\.\/img\/?([\w-_]*\/)$fileName\.$extension/m";

					if ( preg_match( $pattern, $compiled ) ) {
						$compiled = preg_replace( $pattern, $fullMatchAndUrl, $compiled );
					}
				}
			}

			return $compiled;
		}

		/**
		 * Apply styles from admin in frontend.
		 *
		 * @return bool
		 */
		public static function apply_style_on_design_change() {
			$hash        = static::get_design_hash();
			$filePattern = 'main-custom-%s.min.css';
			$path        = AnyComment()->plugin_path() . '/static/css/';

			$fullPath = $path . sprintf( $filePattern, $hash );

			if ( ! file_exists( $fullPath ) ) {

				// Need to check whether files with such patter already exist and delete
				// to avoid duplicate unwanted files
				$oldCustomFiles = glob( $path . sprintf( $filePattern, '*' ) );

				$generatedCss = static::combine_styles_and_process();

				if ( empty( $generatedCss ) ) {
					return false;
				}

				if ( ! empty( $oldCustomFiles ) ) {
					foreach ( $oldCustomFiles as $key => $oldFile ) {
						unlink( $oldFile );
					}
				}

				$fileSaved = file_put_contents( $fullPath, $generatedCss );

				return $fileSaved !== false;
			}

			return false;
		}

		/**
		 * Get design hash to check whether it was changed or not.
		 *
		 * @return string
		 */
		public static function get_design_hash() {
			$items = [];

			$items[] = AnyComment()->version;

			$options = static::instance()->get_options();

			if ( ! empty( $options ) ) {
				foreach ( $options as $option_name => $option_value ) {
					if ( strpos( $option_name, '_design_' ) !== false ) {
						$items[ $option_name ] = $option_value;
					}
				}
			}

			return md5( serialize( $items ) );
		}

		/**
		 * Get custom design hash.
		 *
		 * @param bool $createOnNotFound Generate stylesheets if do not exist.
		 *
		 * @return null|string NULL on failure (when nothing in the design specified yet.
		 */
		public static function get_custom_design_stylesheet_url( $createOnNotFound = true ) {

			$hash = static::get_design_hash();

			if ( empty( $hash ) ) {
				return null;
			}

			$relativePath = sprintf( '/static/css/main-custom-%s.min.css', $hash );

			$sheetsPath = AnyComment()->plugin_path() . $relativePath;

			if ( $createOnNotFound && ! file_exists( $sheetsPath ) ) {
				static::apply_style_on_design_change();
			}

			return AnyComment()->plugin_url() . $relativePath;
		}

		/**
		 * Check whether plugin is enabled or not.
		 *
		 * @return bool
		 */
		public static function is_enabled() {
			return static::instance()->get_option( self::OPTION_PLUGIN_TOGGLE ) !== null;
		}

		/**
		 * Check whether it is required to load comments on scroll.
		 *
		 * @return bool
		 */
		public static function is_load_on_scroll() {
			return static::instance()->get_option( self::OPTION_LOAD_ON_SCROLL ) !== null;
		}

		/**
		 * Check whether it is required to hold comments with links for moderation.
		 *
		 * @return bool
		 */
		public static function is_links_on_hold() {
			return static::instance()->get_option( self::OPTION_LINKS_ON_HOLD ) !== null;
		}

		/**
		 * Check whether it is required to mark comments for moderation.
		 *
		 * @return bool
		 */
		public static function is_moderate_first() {
			return static::instance()->get_option( self::OPTION_MODERATE_FIRST ) !== null;
		}

		/**
		 * Check whether it is required to show video attachments.
		 *
		 * @return bool
		 */
		public static function is_show_twitter_embeds() {
			return static::instance()->get_option( self::OPTION_SHOW_TWITTER_EMBEDS ) !== null;
		}

		/**
		 * Check whether it is required to show video attachments.
		 *
		 * @return bool
		 */
		public static function is_show_video_attachments() {
			return static::instance()->get_option( self::OPTION_SHOW_VIDEO_ATTACHMENTS ) !== null;
		}

		/**
		 * Check whether it is required to show image attachments.
		 *
		 * @return bool
		 */
		public static function is_show_image_attachments() {
			return static::instance()->get_option( self::OPTION_SHOW_IMAGE_ATTACHMENTS ) !== null;
		}

		/**
		 * Check whether it is required to make links clickable.
		 *
		 * @return bool
		 */
		public static function is_link_clickable() {
			return static::instance()->get_option( self::OPTION_MAKE_LINKS_CLICKABLE ) !== null;
		}

		/**
		 * Check whether it is required to show social profile URL or not.
		 *
		 * @return bool
		 */
		public static function is_show_profile_url() {
			return static::instance()->get_option( self::OPTION_SHOW_PROFILE_URL ) !== null;
		}

		/**
		 * Check whether it is required to show list of social icons in the login page.
		 *
		 * @return bool
		 */
		public static function is_show_socials_in_login_page() {
			return static::instance()->get_option( self::OPTION_SHOW_SOCIALS_IN_LOGIN_PAGE ) !== null;
		}

		/**
		 * Check whether it is required to show admin bar.
		 *
		 * @return bool
		 */
		public static function is_show_admin_bar() {
			return static::instance()->get_option( self::OPTION_SHOW_ADMIN_BAR ) !== null;
		}

		/**
		 * Check whether it is required to notify with alert on new comment.
		 *
		 * @return bool
		 */
		public static function is_notify_on_new_comment() {
			return static::instance()->get_option( self::OPTION_NOTIFY_ON_NEW_COMMENT ) !== null;
		}

		/**
		 * Check whether it is required to notify administrator about new comment.
		 *
		 * @return bool
		 */
		public static function is_notify_admin() {
			return static::instance()->get_option( self::OPTION_NOTIFY_ADMINISTRATOR ) !== null;
		}

		/**
		 * Check whether it is required to notify subscribers about new comment(s).
		 *
		 * @return bool
		 */
		public static function is_notify_subscribers() {
			return static::instance()->get_option( self::OPTION_NOTIFY_SUBSCRIBERS ) !== null;
		}

		/**
		 * Get sender name. When name is empty, `blogname` options will be returned.
		 * @return string
		 */
		public static function get_notify_email_sender_name() {
			$value = static::instance()->get_option( self::OPTION_NOTIFY_SUBSCRIBERS );

			if ( empty( $value ) ) {
				// Get blog name in case from name is now specified
				return get_option( 'blogname' );
			}

			return $value;
		}

		/**
		 * Get subscribers email template format.
		 *
		 * @return string|null
		 */
		public static function get_notify_email_subscribers_template() {
			return static::instance()->get_option( self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE );
		}

		/**
		 * Check whether it is required to notify by sending email on new reply.
		 *
		 * @return bool
		 */
		public static function is_notify_on_new_reply() {
			return static::instance()->get_option( self::OPTION_NOTIFY_ON_NEW_REPLY ) !== null;
		}

		/**
		 * Get admin email template format.
		 *
		 * @return string|null
		 */
		public static function get_notify_email_admin_template() {
			return static::instance()->get_option( self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE );
		}

		/**
		 * Get reply email template format.
		 *
		 * @return string|null
		 */
		public static function get_notify_email_reply_template() {
			return static::instance()->get_option( self::OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE );
		}

		/**
		 * Get list of words to moderate.
		 *
		 * @return string|null
		 */
		public static function get_moderate_words() {
			return static::instance()->get_option( self::OPTION_MODERATE_WORDS );
		}

		/**
		 * Check whether file upload is allowed.
		 *
		 * @return bool
		 */
		public static function is_file_upload_allowed() {
			return static::instance()->get_option( self::OPTION_FILES_TOGGLE ) !== null;
		}

		/**
		 * Check whether guests uses can upload files.
		 *
		 * @return bool
		 */
		public static function is_guest_can_upload() {
			return static::instance()->get_option( self::OPTION_FILES_GUEST_CAN_UPLOAD );
		}

		/**
		 * Get file max size.
		 *
		 * @return float|null
		 */
		public static function get_file_max_size() {
			return static::instance()->get_option( self::OPTION_FILES_MAX_SIZE );
		}

		/**
		 * Get file upload limit.
		 *
		 * @return float|null
		 */
		public static function get_file_limit() {
			return static::instance()->get_option( self::OPTION_FILES_LIMIT );
		}

		/**
		 * Get file upload period limit in seconds.
		 *
		 * @return int|null
		 */
		public static function get_file_upload_limit() {
			return static::instance()->get_option( self::OPTION_FILES_LIMIT_PERIOD );
		}

		/**
		 * Get allowed file MIME types.
		 *
		 * @return string|null
		 */
		public static function get_file_mime_types() {
			return static::instance()->get_option( self::OPTION_FILES_MIME_TYPES );
		}

		/**
		 * Method is used to check for correctness of the file mime type again what is defined in settigs.
		 *
		 * @link https://github.com/okonet/attr-accept/blob/master/src/index.js (credits, used in frontend)
		 * @since 0.0.52
		 *
		 * @param array $file Regular array item from $_FILE
		 *
		 * @return bool
		 */
		public static function is_allowed_mime_type( $file ) {
			$acceptedFilesArray = explode( ',', static::get_file_mime_types() );

			if ( empty( $acceptedFilesArray ) ) {
				return false;
			}

			$fileName     = isset( $file['name'] ) ? $file['name'] : null;
			$mimeType     = isset( $file['type'] ) ? $file['type'] : null;
			$baseMimeType = preg_replace( '/\/.*$/', '', $mimeType );
			$successCount = 0;

			foreach ( $acceptedFilesArray as $key => $type ) {
				$validType = trim( $type );
				if ( $validType{0} === '.' ) {
					if ( strpos( strtolower( $fileName ), strtolower( $validType ) ) !== false ) {
						$successCount ++;
					}
				} else if ( strpos( $validType, '/*' ) !== false ) {
					// This is something like a image/* mime type
					if ( $baseMimeType === preg_replace( '/\/.*$/', '', $validType ) ) {
						$successCount ++;
					}
				}

				if ( $mimeType === $validType ) {
					$successCount ++;
				}
			}

			return $successCount > 0;
		}

		/**
		 * Get list of enabled options to react editor.
		 *
		 * @return array
		 */
		public static function get_editor_toolbar_options() {

			$toolbar_option = [];
			if ( static::is_editor_toolbar_bold() ) {
				$toolbar_option[] = 'bold';
			}

			if ( static::is_editor_toolbar_italic() ) {
				$toolbar_option[] = 'italic';
			}

			if ( static::is_editor_toolbar_underline() ) {
				$toolbar_option[] = 'underline';
			}

			if ( static::is_editor_toolbar_blockquote() ) {
				$toolbar_option[] = 'blockquote';
			}

			if ( static::is_editor_toolbar_ordered_list() ) {
				$toolbar_option[] = 'ordered';
			}

			if ( static::is_editor_toolbar_bullet_list() ) {
				$toolbar_option[] = 'bullet';
			}

			if ( static::is_editor_toolbar_link() ) {
				$toolbar_option[] = 'link';
			}

			if ( static::is_editor_toolbar_clean() ) {
				$toolbar_option[] = 'clean';
			}

			return $toolbar_option;
		}

		/**
		 * Check whether editor toolbar is on.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_on() {
			$is_toolbar_on                 = static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_TOGGLE ) !== null;
			$has_at_least_one_toolbar_item = static::is_editor_toolbar_bold() ||
			                                 static::is_editor_toolbar_italic() ||
			                                 static::is_editor_toolbar_underline() ||
			                                 static::is_editor_toolbar_blockquote() ||
			                                 static::is_editor_toolbar_ordered_list() ||
			                                 static::is_editor_toolbar_bullet_list() ||
			                                 static::is_editor_toolbar_link() ||
			                                 static::is_editor_toolbar_clean();

			if ( $is_toolbar_on && $has_at_least_one_toolbar_item ) {
				return true;
			}

			return false;
		}

		/**
		 * Check whether bold option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_bold() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_BOLD ) !== null;
		}

		/**
		 * Check whether italic option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_italic() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_ITALIC ) !== null;
		}

		/**
		 * Check whether underline option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_underline() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_UNDERLINE ) !== null;
		}

		/**
		 * Check whether blockquote option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_blockquote() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_QUOTE ) !== null;
		}

		/**
		 * Check whether ordered list option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_ordered_list() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_ORDERED ) !== null;
		}

		/**
		 * Check whether bullet list option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_bullet_list() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_BULLET ) !== null;
		}

		/**
		 * Check whether link option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_link() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_LINK ) !== null;
		}

		/**
		 * Check whether clean option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function is_editor_toolbar_clean() {
			return static::instance()->get_option( self::OPTION_EDITOR_TOOLBAR_CLEAN ) !== null;
		}


		/**
		 * Enable custom design.
		 *
		 * @return bool
		 */
		public static function is_design_custom() {
			return static::instance()->get_option( self::OPTION_DESIGN_CUSTOM_TOGGLE ) !== null;
		}

		/**
		 * Get global background color.
		 *
		 * @return string
		 */
		public static function get_global_background_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_GLOBAL_BACKGROUND_COLOR ) );
		}

		/**
		 * Get global background border radius.
		 *
		 * @return string
		 */
		public static function get_global_background_border_radius() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS ) );
		}

		/**
		 * Get global margin.
		 *
		 * @return string
		 */
		public static function get_global_margin() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_GLOBAL_MARGIN ) );
		}

		/**
		 * Get global padding.
		 *
		 * @return string
		 */
		public static function get_global_padding() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_GLOBAL_PADDING ) );
		}

		/**
		 * Get design font size.
		 *
		 * @return string|null
		 */
		public static function get_design_font_size() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_FONT_SIZE ) );
		}

		/**
		 * Get design font family size.
		 *
		 * @return string|null
		 */
		public static function get_design_font_family() {
			return static::instance()->get_option( self::OPTION_DESIGN_FONT_FAMILY );
		}

		/**
		 * Get design semi hidden color.
		 *
		 * @return string|null
		 */
		public static function get_design_semi_hidden_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_SEMI_HIDDEN_COLOR ) );
		}


		/**
		 * Get link color.
		 *
		 * @return string|null
		 */
		public static function get_design_link_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_LINK_COLOR ) );
		}

		/**
		 * Get text color.
		 *
		 * @return string|null
		 */
		public static function get_design_text_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_TEXT_COLOR ) );
		}

		/**
		 * Get design form field background color.
		 *
		 * @return string|null
		 */
		public static function get_design_form_field_background_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR ) );
		}

		/**
		 * Get design attachment color.
		 *
		 * @return string|null
		 */
		public static function get_design_attachment_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_ATTACHMENT_COLOR ) );
		}

		/**
		 * Get design attachment background color.
		 *
		 * @return string|null
		 */
		public static function get_design_attachment_background_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR ) );
		}

		/**
		 * Get design avatar border radius.
		 *
		 * @return string|null
		 */
		public static function get_design_avatar_radius() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_AVATAR_RADIUS ) );
		}

		/**
		 * Get design parent avatar size.
		 *
		 * @return string|null
		 */
		public static function get_design_parent_avatar_size() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_PARENT_AVATAR_SIZE ) );
		}

		/**
		 * Get design child avatar size.
		 *
		 * @return string|null
		 */
		public static function get_design_child_avatar_size() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_CHILD_AVATAR_SIZE ) );
		}

		/**
		 * Get design button color.
		 *
		 * @return string|null
		 */
		public static function get_design_button_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_BUTTON_COLOR ) );
		}

		/**
		 * Get design button background color.
		 *
		 * @return string|null
		 */
		public static function get_design_button_background_color() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR ) );
		}

		/**
		 * Get design button background color color.
		 *
		 * @return string|null
		 */
		public static function get_design_button_background_color_active() {
			return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_option( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE ) );
		}

		/**
		 * Get design button radius.
		 *
		 * @return string|null
		 */
		public static function get_design_button_radius() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_BUTTON_RADIUS ) );
		}

		/**
		 * Get design button radius.
		 *
		 * @return string|null
		 */
		public static function get_design_global_radius() {
			return AnyCommentInputHelper::normalize_css_size( static::instance()->get_option( self::OPTION_DESIGN_GLOBAL_RADIUS ) );
		}

		/**
		 * Get interval in seconds per each check for new comments.
		 *
		 * @see AnyCommentGenericSettings::is_notify_on_new_reply() for more information. Which option is ignored when notification disabled.
		 *
		 * @return string
		 */
		public static function get_interval_comments_check() {
			$intervalInSeconds = static::instance()->get_option( self::OPTION_INTERVAL_COMMENTS_CHECK );

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
		public static function get_register_default_group() {
			return static::instance()->get_option( self::OPTION_REGISTER_DEFAULT_GROUP );
		}

		/**
		 * Get user agreement link. Used when user is guest and be authorizing using social network.
		 *
		 * @return string|null
		 */
		public static function get_user_agreement_link() {
			return static::instance()->get_option( self::OPTION_USER_AGREEMENT_LINK );
		}

		/**
		 * Check whether read more should be shown.
		 *
		 * @return bool
		 */
		public static function is_read_more_on() {
			return static::instance()->get_option( self::OPTION_READ_MORE_TOGGLE ) !== null;
		}

		/**
		 * Check whether rating should be displayed or not.
		 *
		 * @return bool
		 */
		public static function is_rating_on() {
			return static::instance()->get_option( self::OPTION_RATING_TOGGLE ) !== null;
		}

		/**
		 * Get comment loaded per page setting value.
		 *
		 * @return int
		 */
		public static function get_per_page() {
			$value = (int) static::instance()->get_option( self::OPTION_COUNT_PER_PAGE );

			if ( $value < 5 ) {
				$value = 5;
			}

			return $value;
		}

		/**
		 * Get comment update time in minutes.
		 *
		 * @return int
		 */
		public static function get_comment_update_time() {
			$value = (int) static::instance()->get_option( self::OPTION_COMMENT_UPDATE_TIME );

			if ( empty( $value ) || (int) $value === 0 || $value < 1 ) {
				$value = 0;
			}

			return $value;
		}


		/**
		 * Get default sort order.
		 *
		 * @return string
		 */
		public static function get_sort_order() {
			$value = static::instance()->get_option( self::OPTION_DEFAULT_SORT_BY );

			if ( $value !== self::SORT_DESC && $value !== self::SORT_ASC ) {
				return self::SORT_DESC;
			}

			return $value;
		}

		/**
		 * Check whether default avatar is anycomment.
		 *
		 * @return bool
		 */
		public static function is_default_avatar_anycomment() {
			return static::get_default_avatar() === self::OPTION_DEFAULT_AVATAR_ANYCOMMENT;
		}

		/**
		 * Get default avatar option.
		 *
		 * @return null|string
		 */
		public static function get_default_avatar() {
			$value = static::instance()->get_option( self::OPTION_DEFAULT_AVATAR );

			if ( $value !== self::OPTION_DEFAULT_AVATAR_ANYCOMMENT &&
			     $value !== self::OPTION_DEFAULT_AVATAR_MP &&
			     $value !== self::OPTION_DEFAULT_AVATAR_IDENTICON &&
			     $value !== self::OPTION_DEFAULT_AVATAR_MONSTEROID &&
			     $value !== self::OPTION_DEFAULT_AVATAR_WAVATAR &&
			     $value !== self::OPTION_DEFAULT_AVATAR_RETRO &&
			     $value !== self::OPTION_DEFAULT_AVATAR_ROBOHASH ) {
				return self::OPTION_DEFAULT_AVATAR_ANYCOMMENT;
			}

			return $value;
		}

		/**
		 * Get form type.
		 *
		 * @return string|null
		 */
		public static function get_form_type() {
			return static::instance()->get_option( self::OPTION_FORM_TYPE );
		}

		/**
		 * Get form type.
		 *
		 * Expected to have:
		 * - {name} - for name input field
		 * - {email} - for user email input field
		 * - {website} - for user website input field
		 *
		 * @param bool $asArray If required to return as array list of params.
		 *
		 * @return string|array|null
		 */
		public static function get_guest_fields( $asArray = false ) {
			$instance = static::instance();
			$value    = $instance->get_option( self::OPTION_GUEST_FIELDS );

			/**
			 * Name is required. If there is no name,
			 * it should be added.
			 */
			if ( strpos( $value, '{name}' ) === false ) {
				$value = '{name} ' . $value;
			}

			preg_match_all( '/\{(name|email|website)\}/', $value, $matches );

			if ( ! $asArray && empty( $matches[1] ) ) {
				return $instance->default_options[ self::OPTION_GUEST_FIELDS ];
			}

			if ( ! $asArray ) {
				return $value;
			}

			return array_slice( $matches[1], 0, 3 );
		}

		/**
		 * Check whether name is in the list of guest fields.
		 *
		 * @return bool
		 */
		public static function is_guest_field_name_on() {
			return in_array( 'name', static::get_guest_fields( true ), true );
		}

		/**
		 * Check whether email is in the list of guest fields.
		 *
		 * @return bool
		 */
		public static function is_guest_field_email_on() {
			return in_array( 'email', static::get_guest_fields( true ), true );
		}

		/**
		 * Check whether website is in the list of guest fields.
		 *
		 * @return bool
		 */
		public static function is_guest_field_website_on() {
			return in_array( 'website', static::get_guest_fields( true ), true );
		}

		/**
		 * Check whether form type is for all.
		 *
		 * @return bool
		 */
		public static function is_form_type_all() {
			return static::get_form_type() === self::FORM_OPTION_ALL;
		}

		/**
		 * Check whether form type is for authorized WordPress users only.
		 *
		 * @return bool
		 */
		public static function is_form_type_wordpress() {
			return static::get_form_type() === self::FORM_OPTION_WORDPRESS_ONLY;
		}

		/**
		 * Check whether form type is for social only.
		 *
		 * @return bool
		 */
		public static function is_form_type_socials() {
			return static::get_form_type() === self::FORM_OPTION_SOCIALS_ONLY;
		}

		/**
		 * Check whether form type is for guests only.
		 *
		 * @return bool
		 */
		public static function is_form_type_guests() {
			return static::get_form_type() === self::FORM_OPTION_GUEST_ONLY;
		}

		/**
		 * Check whether copyright should on or not.
		 *
		 * @return bool
		 */
		public static function is_copyright_on() {
			return static::instance()->get_option( self::OPTION_COPYRIGHT_TOGGLE ) !== null;
		}
	}
endif;

