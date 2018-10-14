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
		 * Reply email template.
		 */
		const OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE = 'option_notify_reply_email_template';

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
		 * Ability to toggle read more.
		 */
		const OPTION_READ_MORE_TOGGLE = 'option_read_more_toggle';

		/**
		 * Display page rating.
		 */
		const OPTION_RATING_TOGGLE = 'option_rating_toggle';

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
		 * Put comments with links on hold.
		 */
		const OPTION_LINKS_ON_HOLD = 'options_links_on_hold';

		/**
		 * Show/hide profile URL on client mini social icon.
		 */
		const OPTION_SHOW_PROFILE_URL = 'options_show_profile_url';

		/**
		 * Show tweet (from Twitter) attachments
		 */
		const OPTION_SHOW_TWITTER_EMBEDS = 'options_show_tweet_attachments';

		/**
		 * Show/hide video attachments.
		 */
		const OPTION_SHOW_VIDEO_ATTACHMENTS = 'options_show_video_attachments';

		/**
		 * Show/hide image attachments.
		 */
		const OPTION_SHOW_IMAGE_ATTACHMENTS = 'options_show_image_attachments';

		/**
		 * Whether required to make links clickable.
		 */
		const OPTION_MAKE_LINKS_CLICKABLE = 'options_make_links_clickable';


		/**
		 * FILES UPLOAD
		 */
		const OPTION_FILES_TOGGLE = 'options_files_toggle';
		const OPTION_FILES_GUEST_CAN_UPLOAD = 'options_files_guest_can_upload';
		const OPTION_FILES_MIME_TYPES = 'options_files_mime_types';
		const OPTION_FILES_LIMIT = 'options_files_limit';
		const OPTION_FILES_LIMIT_PERIOD = 'options_files_limit_period';
		const OPTION_FILES_MAX_SIZE = 'options_files_max_size';

		/**
		 * DESIGN
		 */
		/**
		 * Define form type: only guest users, only social networks or both of it.
		 */
		const OPTION_FORM_TYPE = 'options_form_type';

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
		 * Define what fields to show and order.
		 */
		const OPTION_GUEST_FIELDS = 'options_guest_fields';

		/**
		 * Custom design options.
		 */
		const OPTION_DESIGN_CUSTOM_TOGGLE = 'options_design_custom_toggle';

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
			self::OPTION_COPYRIGHT_TOGGLE        => 'on',
			self::OPTION_COUNT_PER_PAGE          => 20,
			self::OPTION_INTERVAL_COMMENTS_CHECK => 10,

			self::OPTION_DEFAULT_SORT_BY                       => self::SORT_DESC,

			// Files
			self::OPTION_FILES_LIMIT                           => 5,
			self::OPTION_FILES_LIMIT_PERIOD                    => 900,
			self::OPTION_FILES_MAX_SIZE                        => 1.5,
			self::OPTION_FILES_MIME_TYPES                      => 'image/*, .pdf',

			// Notifications
			self::OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE           => "New reply for you in {blogUrlHtml}.\nFrom post {postUrlHtml}.\n\n{commentFormatted}\n{replyButton}",
			self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE           => "New comment posted in {blogUrlHtml}.\nFor post {postUrlHtml}.\n\n{commentFormatted}\n{replyButton}",

			// Other design
			self::OPTION_FORM_TYPE                             => self::FORM_OPTION_SOCIALS_ONLY,
			self::OPTION_GUEST_FIELDS                          => '{name} {email} {website}',

			// Editor
//			self::OPTION_EDITOR_TOOLBAR_TOGGLE                 => 'on',
//			self::OPTION_EDITOR_TOOLBAR_BOLD                   => 'on',
//			self::OPTION_EDITOR_TOOLBAR_ITALIC                 => 'on',
//			self::OPTION_EDITOR_TOOLBAR_UNDERLINE              => 'on',
//			self::OPTION_EDITOR_TOOLBAR_QUOTE                  => 'on',
//			self::OPTION_EDITOR_TOOLBAR_ORDERED                => 'on',
//			self::OPTION_EDITOR_TOOLBAR_BULLET                 => 'on',
//			self::OPTION_EDITOR_TOOLBAR_LINK                   => 'on',
//			self::OPTION_EDITOR_TOOLBAR_CLEAN                  => 'on',

			// Custom design
			self::OPTION_DESIGN_FONT_SIZE                      => '15px',
			self::OPTION_DESIGN_FONT_FAMILY                    => "'Noto-Sans', sans-serif",
			self::OPTION_DESIGN_SEMI_HIDDEN_COLOR              => '#b6c1c6',
			self::OPTION_DESIGN_LINK_COLOR                     => '#3658f7',
			self::OPTION_DESIGN_TEXT_COLOR                     => '#333333',
			self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR    => '#ffffff',
			self::OPTION_DESIGN_ATTACHMENT_COLOR               => '#eeeeee',
			self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR    => '#eeeeee',
			self::OPTION_DESIGN_AVATAR_RADIUS                  => '50%',
			self::OPTION_DESIGN_PARENT_AVATAR_SIZE             => '60px',
			self::OPTION_DESIGN_CHILD_AVATAR_SIZE              => '48px',
			self::OPTION_DESIGN_BUTTON_COLOR                   => '#ffffff',
			self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR        => '#53af4a',
			self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE => '#4f9f49',
			self::OPTION_DESIGN_BUTTON_RADIUS                  => '40px',
			self::OPTION_DESIGN_GLOBAL_RADIUS                  => '4px',
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
				null,
				$this->page_slug
			);

			add_settings_section(
				'section_design',
				__( 'Design', "anycomment" ),
				null,
				$this->page_slug
			);

			add_settings_section(
				'section_moderation',
				__( 'Moderation', "anycomment" ),
				null,
				$this->page_slug
			);

			add_settings_section(
				'section_notifications',
				__( 'Notifications', "anycomment" ),
				null,
				$this->page_slug
			);

			add_settings_section(
				'section_files',
				__( 'Files', "anycomment" ),
				null,
				$this->page_slug
			);


			$this->render_fields(
				$this->page_slug,
				'section_generic',
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
				$this->page_slug,
				'section_design',
				[
					[
						'id'          => self::OPTION_FORM_TYPE,
						'title'       => __( 'Comment form', "anycomment" ),
						'type'        => 'select',
						'options'     => [
							self::FORM_OPTION_ALL          => __( 'Social, WordPress & guests', 'anycomment' ),
							self::FORM_OPTION_SOCIALS_ONLY => __( 'Socials & WordPress users only.', 'anycomment' ),
							self::FORM_OPTION_GUEST_ONLY   => __( 'Guests only. ', 'anycomment' ),
						],
						'description' => esc_html( __( 'Comment form', "anycomment" ) ),
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

					[
						'id'          => self::OPTION_DESIGN_CUSTOM_TOGGLE,
						'title'       => __( 'Custom Design', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Use custom design. Enable this option to display design changes from below.', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_DESIGN_GLOBAL_RADIUS,
						'title'       => __( 'Border radius', "anycomment" ),
						'type'        => 'text',
						'description' => esc_html( __( 'Border radius. You may use "px" or "%".', "anycomment" ) )
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
				$this->page_slug,
				'section_moderation',
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
				$this->page_slug,
				'section_notifications',
				[
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
						'title'       => __( 'Email Notifications', "anycomment" ),
						'type'        => 'checkbox',
						'description' => esc_html( __( 'Notify users by email (if specified) about new replies. Make sure you have proper SMTP configurations in order to send emails.', "anycomment" ) ),
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

							$id = self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE . time();

							$html = '<div><span class="button button-small" id="' . $id . '">' . __( 'More info', 'anycomment' ) . '</span><ul style="display: none;">';

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
				$this->page_slug,
				'section_files',
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

			if ( AnyCommentGenericSettings::isDesignCustom() ) {
				static::applyStyleOnDesignChange();
			}

			settings_errors( $this->alert_key );
			?>
			<?php if ( $wrapper ): ?>
                <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php endif; ?>
            <form action="options.php" method="post" class="anycomment-form">
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
		private static function combineStylesAndProcess() {
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
				'font-size'   => AnyCommentGenericSettings::getDesignFontSize(),
				'font-family' => AnyCommentGenericSettings::getDesignFontFamily(),
				'link-color'  => AnyCommentGenericSettings::getDesignLinkColor(),
				'text-color'  => AnyCommentGenericSettings::getDesignTextColor(),

				'semi-hidden-color' => AnyCommentGenericSettings::getDesignSemiHiddenColor(),

				'form-field-background-color' => AnyCommentGenericSettings::getDesignFormFieldBackgroundColor(),

				'attachment-color'            => AnyCommentGenericSettings::getDesignAttachmentColor(),
				'attachment-background-color' => AnyCommentGenericSettings::getDesignAttachmentBackgroundColor(),

				'avatar-border-radius' => AnyCommentGenericSettings::getDesignAvatarRadius(),
				'parent-avatar-size'   => AnyCommentGenericSettings::getDesignParentAvatarSize(),
				'child-avatar-size'    => AnyCommentGenericSettings::getDesignChildAvatarSize(),

				'btn-radius'                  => AnyCommentGenericSettings::getDesignButtonRadius(),
				'btn-color'                   => AnyCommentGenericSettings::getDesignButtonColor(),
				'btn-background-color'        => AnyCommentGenericSettings::getDesignButtonBackgroundColor(),
				'btn-background-color-active' => AnyCommentGenericSettings::getDesignButtonBackgroundColorActive(),

				'global-radius' => AnyCommentGenericSettings::getDesignGlobalRadius(),
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
		public static function applyStyleOnDesignChange() {
			$hash        = static::getDesignHash();
			$filePattern = 'main-custom-%s.min.css';
			$path        = AnyComment()->plugin_path() . '/static/css/';

			$fullPath = $path . sprintf( $filePattern, $hash );

			if ( ! file_exists( $fullPath ) ) {

				// Need to check whether files with such patter already exist and delete
				// to avoid duplicate unwanted files
				$oldCustomFiles = glob( $path . sprintf( $filePattern, '*' ) );

				$generatedCss = static::combineStylesAndProcess();

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
		public static function getDesignHash() {
			$items = [];

			$items[] = AnyComment()->version;

			$options = static::instance()->getOptions();

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
		public static function getCustomDesignStylesheetUrl( $createOnNotFound = true ) {

			$hash = static::getDesignHash();

			if ( empty( $hash ) ) {
				return null;
			}

			$relativePath = sprintf( '/static/css/main-custom-%s.min.css', $hash );

			$sheetsPath = AnyComment()->plugin_path() . $relativePath;

			if ( $createOnNotFound && ! file_exists( $sheetsPath ) ) {
				static::applyStyleOnDesignChange();
			}

			return AnyComment()->plugin_url() . $relativePath;
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
		 * Check whether it is required to hold comments with links for moderation.
		 *
		 * @return bool
		 */
		public static function isLinksOnHold() {
			return static::instance()->getOption( self::OPTION_LINKS_ON_HOLD ) !== null;
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
		 * Check whether it is required to show video attachments.
		 *
		 * @return bool
		 */
		public static function isShowTwitterEmbeds() {
			return static::instance()->getOption( self::OPTION_SHOW_TWITTER_EMBEDS ) !== null;
		}

		/**
		 * Check whether it is required to show video attachments.
		 *
		 * @return bool
		 */
		public static function isShowVideoAttachments() {
			return static::instance()->getOption( self::OPTION_SHOW_VIDEO_ATTACHMENTS ) !== null;
		}

		/**
		 * Check whether it is required to show image attachments.
		 *
		 * @return bool
		 */
		public static function isShowImageAttachments() {
			return static::instance()->getOption( self::OPTION_SHOW_IMAGE_ATTACHMENTS ) !== null;
		}

		/**
		 * Check whether it is required to make links clickable.
		 *
		 * @return bool
		 */
		public static function isLinkClickable() {
			return static::instance()->getOption( self::OPTION_MAKE_LINKS_CLICKABLE ) !== null;
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
		 * Get admin email template format.
		 *
		 * @return string|null
		 */
		public static function getNotifyEmailAdminTemplate() {
			return static::instance()->getOption( self::OPTION_NOTIFY_ADMIN_EMAIL_TEMPLATE );
		}

		/**
		 * Get reply email template format.
		 *
		 * @return string|null
		 */
		public static function getNotifyEmailReplyTemplate() {
			return static::instance()->getOption( self::OPTION_NOTIFY_REPLY_EMAIL_TEMPLATE );
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
		 * Check whether file upload is allowed.
		 *
		 * @return bool
		 */
		public static function isFileUploadAllowed() {
			return static::instance()->getOption( self::OPTION_FILES_TOGGLE ) !== null;
		}

		/**
		 * Check whether guests uses can upload files.
		 *
		 * @return bool
		 */
		public static function isGuestCanUpload() {
			return static::instance()->getOption( self::OPTION_FILES_GUEST_CAN_UPLOAD );
		}

		/**
		 * Get file max size.
		 *
		 * @return float|null
		 */
		public static function getFileMaxSize() {
			return static::instance()->getOption( self::OPTION_FILES_MAX_SIZE );
		}

		/**
		 * Get file upload limit.
		 *
		 * @return float|null
		 */
		public static function getFileLimit() {
			return static::instance()->getOption( self::OPTION_FILES_LIMIT );
		}

		/**
		 * Get file upload period limit in seconds.
		 *
		 * @return int|null
		 */
		public static function getFileUploadLimit() {
			return static::instance()->getOption( self::OPTION_FILES_LIMIT_PERIOD );
		}

		/**
		 * Get allowed file MIME types.
		 *
		 * @return string|null
		 */
		public static function getFileMimeTypes() {
			return static::instance()->getOption( self::OPTION_FILES_MIME_TYPES );
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
		public static function isAllowedMimeType( $file ) {
			$acceptedFilesArray = explode( ',', static::getFileMimeTypes() );

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
		public static function getEditorToolbarOptions() {

			$toolbar_option = [];
			if ( static::isEditorToolbarBold() ) {
				$toolbar_option[] = 'bold';
			}

			if ( static::isEditorToolbarItalic() ) {
				$toolbar_option[] = 'italic';
			}

			if ( static::isEditorToolbarUnderline() ) {
				$toolbar_option[] = 'underline';
			}

			if ( static::isEditorToolbarBlockQuote() ) {
				$toolbar_option[] = 'blockquote';
			}

			if ( static::isEditorToolbarOrderedList() ) {
				$toolbar_option[] = 'ordered';
			}

			if ( static::isEditorToolbarBulletList() ) {
				$toolbar_option[] = 'bullet';
			}

			if ( static::isEditorToolbarLink() ) {
				$toolbar_option[] = 'link';
			}

			if ( static::isEditorToolbarClean() ) {
				$toolbar_option[] = 'clean';
			}

			return $toolbar_option;
		}

		/**
		 * Check whether editor toolbar is on.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarOn() {
			$is_toolbar_on                 = static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_TOGGLE ) !== null;
			$has_at_least_one_toolbar_item = static::isEditorToolbarBold() ||
			                                 static::isEditorToolbarItalic() ||
			                                 static::isEditorToolbarUnderline() ||
			                                 static::isEditorToolbarBlockQuote() ||
			                                 static::isEditorToolbarOrderedList() ||
			                                 static::isEditorToolbarBulletList() ||
			                                 static::isEditorToolbarLink() ||
			                                 static::isEditorToolbarClean();

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
		public static function isEditorToolbarBold() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_BOLD ) !== null;
		}

		/**
		 * Check whether italic option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarItalic() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_ITALIC ) !== null;
		}

		/**
		 * Check whether underline option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarUnderline() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_UNDERLINE ) !== null;
		}

		/**
		 * Check whether blockquote option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarBlockQuote() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_QUOTE ) !== null;
		}

		/**
		 * Check whether ordered list option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarOrderedList() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_ORDERED ) !== null;
		}

		/**
		 * Check whether bullet list option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarBulletList() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_BULLET ) !== null;
		}

		/**
		 * Check whether link option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarLink() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_LINK ) !== null;
		}

		/**
		 * Check whether clean option should be seen in editor toolbar.
		 *
		 * @return bool
		 */
		public static function isEditorToolbarClean() {
			return static::instance()->getOption( self::OPTION_EDITOR_TOOLBAR_CLEAN ) !== null;
		}


		/**
		 * Enable custom design.
		 *
		 * @return bool
		 */
		public static function isDesignCustom() {
			return static::instance()->getOption( self::OPTION_DESIGN_CUSTOM_TOGGLE ) !== null;
		}

		/**
		 * Get design font size.
		 *
		 * @return string|null
		 */
		public static function getDesignFontSize() {
			return AnyCommentInputHelper::getSizeForCss( static::instance()->getOption( self::OPTION_DESIGN_FONT_SIZE ) );
		}

		/**
		 * Get design font family size.
		 *
		 * @return string|null
		 */
		public static function getDesignFontFamily() {
			return static::instance()->getOption( self::OPTION_DESIGN_FONT_FAMILY );
		}

		/**
		 * Get design semi hidden color.
		 *
		 * @return string|null
		 */
		public static function getDesignSemiHiddenColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_SEMI_HIDDEN_COLOR ) );
		}


		/**
		 * Get link color.
		 *
		 * @return string|null
		 */
		public static function getDesignLinkColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_LINK_COLOR ) );
		}

		/**
		 * Get text color.
		 *
		 * @return string|null
		 */
		public static function getDesignTextColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_TEXT_COLOR ) );
		}

		/**
		 * Get design form field background color.
		 *
		 * @return string|null
		 */
		public static function getDesignFormFieldBackgroundColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR ) );
		}

		/**
		 * Get design attachment color.
		 *
		 * @return string|null
		 */
		public static function getDesignAttachmentColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_ATTACHMENT_COLOR ) );
		}

		/**
		 * Get design attachment background color.
		 *
		 * @return string|null
		 */
		public static function getDesignAttachmentBackgroundColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR ) );
		}

		/**
		 * Get design avatar border radius.
		 *
		 * @return string|null
		 */
		public static function getDesignAvatarRadius() {
			return AnyCommentInputHelper::getSizeForCss( static::instance()->getOption( self::OPTION_DESIGN_AVATAR_RADIUS ) );
		}

		/**
		 * Get design parent avatar size.
		 *
		 * @return string|null
		 */
		public static function getDesignParentAvatarSize() {
			return AnyCommentInputHelper::getSizeForCss( static::instance()->getOption( self::OPTION_DESIGN_PARENT_AVATAR_SIZE ) );
		}

		/**
		 * Get design child avatar size.
		 *
		 * @return string|null
		 */
		public static function getDesignChildAvatarSize() {
			return AnyCommentInputHelper::getSizeForCss( static::instance()->getOption( self::OPTION_DESIGN_CHILD_AVATAR_SIZE ) );
		}

		/**
		 * Get design button color.
		 *
		 * @return string|null
		 */
		public static function getDesignButtonColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_BUTTON_COLOR ) );
		}

		/**
		 * Get design button background color.
		 *
		 * @return string|null
		 */
		public static function getDesignButtonBackgroundColor() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR ) );
		}

		/**
		 * Get design button background color color.
		 *
		 * @return string|null
		 */
		public static function getDesignButtonBackgroundColorActive() {
			return AnyCommentInputHelper::getHexForCss( static::instance()->getOption( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE ) );
		}

		/**
		 * Get design button radius.
		 *
		 * @return string|null
		 */
		public static function getDesignButtonRadius() {
			return AnyCommentInputHelper::getSizeForCss( static::instance()->getOption( self::OPTION_DESIGN_BUTTON_RADIUS ) );
		}

		/**
		 * Get design button radius.
		 *
		 * @return string|null
		 */
		public static function getDesignGlobalRadius() {
			return AnyCommentInputHelper::getSizeForCss( static::instance()->getOption( self::OPTION_DESIGN_GLOBAL_RADIUS ) );
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
		 * Check whether read more should be shown.
		 *
		 * @return bool
		 */
		public static function isReadMoreOn() {
			return static::instance()->getOption( self::OPTION_READ_MORE_TOGGLE ) !== null;
		}

		/**
		 * Check whether rating should be displayed or not.
		 *
		 * @return bool
		 */
		public static function isRatingOn() {
			return static::instance()->getOption( self::OPTION_RATING_TOGGLE ) !== null;
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
		 * Get default sort order.
		 *
		 * @return string
		 */
		public static function getSortOrder() {
			$value = static::instance()->getOption( self::OPTION_DEFAULT_SORT_BY );

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
		public static function isDefaultAvatarAnyComment() {
			return static::getDefaultAvatar() === self::OPTION_DEFAULT_AVATAR_ANYCOMMENT;
		}

		/**
		 * Get default avatar option.
		 *
		 * @return null|string
		 */
		public static function getDefaultAvatar() {
			$value = static::instance()->getOption( self::OPTION_DEFAULT_AVATAR );

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
		public static function getFormType() {
			return static::instance()->getOption( self::OPTION_FORM_TYPE );
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
		public static function getGuestFields( $asArray = false ) {
			$instance = static::instance();
			$value    = $instance->getOption( self::OPTION_GUEST_FIELDS );

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
		public static function isGuestFieldNameOn() {
			return in_array( 'name', static::getGuestFields( true ), true );
		}

		/**
		 * Check whether email is in the list of guest fields.
		 *
		 * @return bool
		 */
		public static function isGuestFieldEmailOn() {
			return in_array( 'email', static::getGuestFields( true ), true );
		}

		/**
		 * Check whether website is in the list of guest fields.
		 *
		 * @return bool
		 */
		public static function isGuestFieldWebsiteOn() {
			return in_array( 'website', static::getGuestFields( true ), true );
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

