<?php

namespace AnyComment\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\AnyCommentServiceApi;
use AnyComment\AnyCommentUploadHandler;
use AnyComment\Base\ScssCompiler;
use AnyComment\Helpers\AnyCommentInputHelper;
use AnyComment\Helpers\AnyCommentTemplate;
use AnyComment\Options\AnyCommentOptionManager;

/**
 * AC_AdminSettingPage helps to process generic plugin settings.
 */
class AnyCommentGenericSettings extends AnyCommentOptionManager {

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
	 * Subscribers email template.
	 */
	const OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE = 'option_notify_subscribe_email_template';

	/**
	 * Subscribers confirmation email template.
	 */
	const OPTION_NOTIFY_SUBSCRIBERS_CONFIRMATION_EMAIL_TEMPLATE = 'option_notify_subscribe_confirmation_email_template';

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

	const OPTION_COMMENT_RATING = 'option_comment_rating';

	const COMMENT_RATING_LIKES = 'likes'; // Likes only
	const COMMENT_RATING_LIKES_DISLIKES = 'likes_dislikes'; // Likes and dislikes

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
	 * Datetime format.
	 */
	const OPTION_COMMENT_DATETIME_FORMAT = 'option_datetime_format';

	const DATETIME_FORMAT_RELATIVE = 'relative'; // e.g. 1 hours ago
	const DATETIME_FORMAT_NATIVE = 'native'; // Native WordPress's, defined in "Settings" -> "Generic"

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
	const OPTION_MODERATE_FIRST_COMMENT_ONLY = 'options_moderate_first_comment'; // Moderate only first comment, any further should pass normally
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
	const OPTION_FILES_SAVE_PATH = 'options_files_save_path';
	const OPTION_FILES_SAVE_SERVE_URL = 'options_files_save_serve_url';

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

	const OPTION_SHOW_UPDATED_INFO = 'options_show_updated_info'; // Show wehther single comment was updated or not

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
	const DEFAULT_ROLE_EDITOR = 'editor'; // (from WordPress)
	const DEFAULT_ROLE_AUTHOR = 'author'; // (from WordPress)
	const DEFAULT_ROLE_CONTRIBUTOR = 'contributor'; // (from WordPress)
	const DEFAULT_ROLE_SOCIAL_SUBSCRIBER = 'social_subscriber'; // Custom social subscriber. Role introduced via this plugin.


	/**
	 * Editor options.
	 */
	const OPTION_EDITOR_CSS = 'option_editor_css'; // Add custom CSS

	const OPTION_SEO_TOGGLE = 'option_seo_toggle';
	const OPTION_SEO_DISPLAY_LIMIT = 'option_seo_display_limit';
	const OPTION_SEO_SORTING = 'option_seo_sorting';

	const SEO_SORTING_NEW2OLD = 'newest_to_old';
	const SEO_SORTING_OLD2NEW = 'old_to_new';

	const GENERIC_OPTION_NAME = 'anycomment-generic';


	/**
	 * @inheritdoc
	 */
	protected $option_group = 'anycomment-generic-group';
	/**
	 * @inheritdoc
	 */
	protected $option_name = self::GENERIC_OPTION_NAME;

	/**
	 * @inheritdoc
	 */
	protected $field_options = [
		'wrapper' => '<div class="cell anycomment-form-wrapper__field">{content}</div>',
	];

	/**
	 * @inheritdoc
	 */
	protected $section_options = [
		'wrapper' => '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab" id="{id}">{content}</div>',
	];


	/**
	 * @inheritdoc
	 */
	protected $default_options = [
		self::OPTION_COPYRIGHT_TOGGLE        => 'on',
		self::OPTION_COMMENT_UPDATE_TIME     => 5,
		self::OPTION_COUNT_PER_PAGE          => 20,
		self::OPTION_INTERVAL_COMMENTS_CHECK => 10,

		self::OPTION_DEFAULT_SORT_BY                                => self::SORT_DESC,
		self::COMMENT_RATING_LIKES                                  => self::COMMENT_RATING_LIKES,

		// Files
		self::OPTION_FILES_LIMIT                                    => 5,
		self::OPTION_FILES_LIMIT_PERIOD                             => 900,
		self::OPTION_FILES_MAX_SIZE                                 => 1.5,
		self::OPTION_FILES_MIME_TYPES                               => 'image/*, .pdf',

		// Notifications
		self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE              => "New comment in {blogUrlHtml}.\nFrom post {postUrlHtml}.\n\n{commentFormatted}\n{replyButton}\n\nYou were subscribed to this post.\n\nYou may unbsubscribe by following this link:\n{unsubscribeUrl}",
		self::OPTION_NOTIFY_SUBSCRIBERS_CONFIRMATION_EMAIL_TEMPLATE => "You were subscribed to {postUrlHtml} on {postTitle}.\n\nPlease follow link below to confirm this action or ignore this message if you think you received this by mistake.\n\n{confirmatiomButton}\n\nOr use link below:\n{confirmationUrl}",

		// Other design
		self::OPTION_FORM_TYPE                                      => self::FORM_OPTION_SOCIALS_ONLY,
		self::OPTION_GUEST_FIELDS                                   => '{name} {email} {website}',
		self::OPTION_SHOW_UPDATED_INFO                              => 'on',

		// Editor
		self::OPTION_EDITOR_TOOLBAR_TOGGLE                          => 'on',
		self::OPTION_EDITOR_TOOLBAR_BOLD                            => 'on',
		self::OPTION_EDITOR_TOOLBAR_ITALIC                          => 'on',
		self::OPTION_EDITOR_TOOLBAR_UNDERLINE                       => 'on',
		self::OPTION_EDITOR_TOOLBAR_QUOTE                           => 'on',
		self::OPTION_EDITOR_TOOLBAR_ORDERED                         => 'on',
		self::OPTION_EDITOR_TOOLBAR_BULLET                          => 'on',
		self::OPTION_EDITOR_TOOLBAR_LINK                            => 'on',
		self::OPTION_EDITOR_TOOLBAR_CLEAN                           => 'on',

		// Custom design
		self::OPTION_DESIGN_GLOBAL_PADDING                          => '0',
		self::OPTION_DESIGN_GLOBAL_MARGIN                           => '20px 0',
		self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS         => '0',
		self::OPTION_DESIGN_FONT_SIZE                               => '14px',
		self::OPTION_DESIGN_FONT_FAMILY                             => "'Noto-Sans', sans-serif",
		self::OPTION_DESIGN_SEMI_HIDDEN_COLOR                       => '#B6C1C6',
		self::OPTION_DESIGN_LINK_COLOR                              => '#1DA1F2',
		self::OPTION_DESIGN_TEXT_COLOR                              => '#2A2E2E',
		self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR             => '#ffffff',
		self::OPTION_DESIGN_ATTACHMENT_COLOR                        => '#eeeeee',
		self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR             => '#eeeeee',
		self::OPTION_DESIGN_AVATAR_RADIUS                           => '50% 50% 50% 0',
		self::OPTION_DESIGN_PARENT_AVATAR_SIZE                      => '48px',
		self::OPTION_DESIGN_CHILD_AVATAR_SIZE                       => '30px',
		self::OPTION_DESIGN_BUTTON_COLOR                            => '#ffffff',
		self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR                 => '#1DA1F2',
		self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE          => '#4f9f49',
		self::OPTION_DESIGN_BUTTON_RADIUS                           => '20px',
		self::OPTION_DESIGN_GLOBAL_RADIUS                           => '10px',

		// SEO
		self::OPTION_SEO_TOGGLE                                     => 'on'
	];

	/**
	 * @inheritdoc
	 */
	protected $page_slug = 'anycomment-settings';


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


		$isSaaSEnabled = AnyCommentServiceApi::is_ready() && AnyCommentIntegrationSettings::is_sass_comments_show();
		$form          = $this->form();

		$form->add_section(
			$this->section_builder()
			     ->set_id( 'generic' )
			     ->set_title( __( 'Generic', "anycomment" ) )
			     ->set_wrapper( '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab current" id="{id}">{content}</div>' )
			     ->set_visible( ! $isSaaSEnabled )
			     ->set_fields( [
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_PLUGIN_TOGGLE )
				          ->set_title( __( 'Enable Comments', "anycomment" ) )
				          ->set_description( esc_html( __( 'When on, comments are visible. When off, default WordPress\' comments shown. This can be used to configure social networks on fresh installation.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->set_id( self::OPTION_DEFAULT_SORT_BY )
				          ->select()
				          ->set_title( __( 'Sorting', 'anycomment' ) )
				          ->set_args( [
					          'options' => [
						          self::SORT_DESC => __( 'Newest first', 'anycomment' ),
						          self::SORT_ASC  => __( 'Oldest first', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'Default sorting.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->set_id( self::OPTION_COMMENT_RATING )
				          ->select()
				          ->set_title( __( 'Comment rating', "anycomment" ) )
				          ->set_args( [
					          'options' => [
						          self::COMMENT_RATING_LIKES          => __( 'Likes only', 'anycomment' ),
						          self::COMMENT_RATING_LIKES_DISLIKES => __( 'Likes and dislikes', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'Type of rating used for single comment. Option "Likes" would display only heart like, whereas "Likes and dislikes" would display up and down options.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->set_id( self::OPTION_DEFAULT_AVATAR )
				          ->select()
				          ->set_title( __( 'Default Avatar', "anycomment" ) )
				          ->set_args( [
					          'options' => [
						          self::OPTION_DEFAULT_AVATAR_ANYCOMMENT => __( 'No avatar (from AnyComment)', 'anycomment' ),
						          self::OPTION_DEFAULT_AVATAR_MP         => __( 'No avatar (from Gravatar)', 'anycomment' ),
						          self::OPTION_DEFAULT_AVATAR_IDENTICON  => __( 'Identicon (from Gravatar)', 'anycomment' ),
						          self::OPTION_DEFAULT_AVATAR_MONSTEROID => __( 'Monsteroid (from Gravatar)', 'anycomment' ),
						          self::OPTION_DEFAULT_AVATAR_WAVATAR    => __( 'Wavatar (from Gravatar)', 'anycomment' ),
						          self::OPTION_DEFAULT_AVATAR_RETRO      => __( 'Retro (from Gravatar)', 'anycomment' ),
						          self::OPTION_DEFAULT_AVATAR_ROBOHASH   => __( 'Robohash (from Gravatar)', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'Default avatar when user does not have any.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->set_id( self::OPTION_REGISTER_DEFAULT_GROUP )
				          ->select()
				          ->set_title( __( 'Register User Group', "anycomment" ) )
				          ->set_args( [
					          'options' => [
						          self::DEFAULT_ROLE_SOCIAL_SUBSCRIBER => __( 'Social Network Subscriber', 'anycomment' ),
						          self::DEFAULT_ROLE_SUBSCRIBER        => __( 'Subscriber', 'anycomment' ),
						          self::DEFAULT_ROLE_EDITOR            => __( 'Editor', 'anycomment' ),
						          self::DEFAULT_ROLE_AUTHOR            => __( 'Author', 'anycomment' ),
						          self::DEFAULT_ROLE_CONTRIBUTOR       => __( 'Contributor', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'When users will authorize via plugin, they are being registered and be assigned with group selected above.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->set_id( self::OPTION_COMMENT_DATETIME_FORMAT )
				          ->select()
				          ->set_title( __( 'Date & Time Format', "anycomment" ) )
				          ->set_args( [
					          'options' => [
						          self::DATETIME_FORMAT_RELATIVE => __( 'Relative (e.g. 1 minute ago)', 'anycomment' ),
						          self::DATETIME_FORMAT_NATIVE   => __( 'Absolute (format taken from WordPress)', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'Choose comment date & time format.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_COMMENT_UPDATE_TIME )
				          ->set_title( __( 'Comment Update Time', "anycomment" ) )
				          ->set_description( esc_html( __( 'Number of minutes user can update his comment. "0" or empty for no limit.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_COUNT_PER_PAGE )
				          ->set_title( __( 'Number of Comments Loaded', "anycomment" ) )
				          ->set_description( esc_html( __( 'Number of comments to load initially and per page.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_LOAD_ON_SCROLL )
				          ->set_title( __( 'Load on Scroll', "anycomment" ) )
				          ->set_description( esc_html( __( 'Load comments when user scrolls to it.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_SOCIALS_IN_LOGIN_PAGE )
				          ->set_title( __( 'Show Login Page Socials', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show list of available socials under WordPress\'s native login form.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_ADMIN_BAR )
				          ->set_title( __( 'Show Admin Bar', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show admin bar for regular WordPress users and those who logged in via social.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_PROFILE_URL )
				          ->set_title( __( 'Show Profile URL', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show link to user in the social media or website when available (name of the user will be clickable).', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_TWITTER_EMBEDS )
				          ->set_title( __( 'Display Twitter Embeds', "anycomment" ) )
				          ->set_description( esc_html( __( 'Detect & display tweets from Twitter as embedded widget.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_VIDEO_ATTACHMENTS )
				          ->set_title( __( 'Display Video Attachments', "anycomment" ) )
				          ->set_description( esc_html( __( 'Display video link from comment as attachment.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_IMAGE_ATTACHMENTS )
				          ->set_title( __( 'Display Image Attachments', "anycomment" ) )
				          ->set_description( esc_html( __( 'Display image link from comment as attachment.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_RATING_TOGGLE )
				          ->set_title( __( 'Display Rating', "anycomment" ) )
				          ->set_description( esc_html( __( 'Display 5 star rating above comments.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_READ_MORE_TOGGLE )
				          ->set_title( __( 'Shorten Long Comments', "anycomment" ) )
				          ->set_description( esc_html( __( 'Shorten long comments with "Read more" message.', "anycomment" ) ) ),


//					$this->field_builder()
//     ->input_checkbox()
//     ->set_id(self::OPTION_MAKE_LINKS_CLICKABLE)
//     ->set_title(__( 'Links Clickable', "anycomment" ))
//     ->set_description(esc_html( __( 'Links in comment are clickable.', "anycomment" ) )),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_USER_AGREEMENT_LINK )
				          ->set_title( __( 'User Agreement Link', "anycomment" ) )
				          ->set_description( esc_html( __( 'Link to User Agreement, where described how your process users data once they authorize via social network and/or add new comment.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_COPYRIGHT_TOGGLE )
				          ->set_title( __( 'Thanks', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show AnyComment\'s link in the footer of comments. Copyright helps to bring awareness of such plugin and bring people to allow us to understand that it is a wanted product and give more often updated.', "anycomment" ) ) ),


			     ] )
		);

		/**
		 * Section: Elements
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'elements' )
			     ->set_title( __( 'Elements', "anycomment" ) )
			     ->set_visible( ! $isSaaSEnabled )
			     ->set_fields( [
				     $this->field_builder()
				          ->select()
				          ->set_id( self::OPTION_FORM_TYPE )
				          ->set_title( __( 'Comment form', "anycomment" ) )
				          ->set_args( [
					          'options' => [
						          self::FORM_OPTION_ALL            => __( 'Anyone', 'anycomment' ),
						          self::FORM_OPTION_WORDPRESS_ONLY => __( 'WordPress only', 'anycomment' ),
						          self::FORM_OPTION_SOCIALS_ONLY   => __( 'Socials only', 'anycomment' ),
						          self::FORM_OPTION_GUEST_ONLY     => __( 'Guests only', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'Users who able to leave comments.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_GUEST_FIELDS )
				          ->set_title( __( 'Guest Fields', "anycomment" ) )
				          ->set_description( esc_html( __( 'Use this rearrange guest form fields or remove something. {name} is required and if you do not add it, it will be added by plugin. {name} is name field, {email} is email field, {website} is website field.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SHOW_UPDATED_INFO )
				          ->set_title( __( 'Show updated info', "anycomment" ) )
				          ->set_description( esc_html__( 'Show updated icon on each comment once it was modified.', "anycomment" ) ),


				     /**
				      * Editor options
				      */
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_TOGGLE )
				          ->set_title( __( 'Enable Toolbar', "anycomment" ) )
				          ->set_description( esc_html( __( 'Enable editor toolbar (show options to modify comment text - bold, italics, etc).', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_BOLD )
				          ->set_title( __( 'Bold', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show bold option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_ITALIC )
				          ->set_title( __( 'Italic', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show italic option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_UNDERLINE )
				          ->set_title( __( 'Underline', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show underline option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_QUOTE )
				          ->set_title( __( 'Quote', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show quote option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_ORDERED )
				          ->set_title( __( 'Ordered list', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show ordered list option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_BULLET )
				          ->set_title( __( 'Unordered list', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show unordered list option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_LINK )
				          ->set_title( __( 'Link', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show link option in editor toolbar.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_EDITOR_TOOLBAR_CLEAN )
				          ->set_title( __( 'Clean formatting', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show clean formatting option in editor toolbar.', "anycomment" ) ) ),
			     ] )
		);

		/**
		 * Section: Design
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'design' )
			     ->set_title( __( 'Design', "anycomment" ) )
			     ->set_visible( ! $isSaaSEnabled )
			     ->set_fields( [
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_DESIGN_CUSTOM_TOGGLE )
				          ->set_title( __( 'Custom Design', "anycomment" ) )
				          ->set_description( esc_html( __( 'Use custom design. Enable this option to display design changes from below.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_FONT_SIZE )
				          ->set_title( __( 'Text Size', "anycomment" ) )
				          ->set_description( esc_html( __( 'Overal text size. You may use "px", "pt", "em" or "%".', "anycomment" ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_FONT_FAMILY )
				          ->set_title( __( 'Font Choice', "anycomment" ) )
				          ->set_description( esc_html( __( 'Global font family.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_TEXT_COLOR )
				          ->set_title( __( 'Text Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Global text color.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_LINK_COLOR )
				          ->set_title( __( 'Link Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Links color.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_GLOBAL_BACKGROUND_COLOR )
				          ->set_title( __( 'Global background color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Global background color used for all comments.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS )
				          ->set_title( __( 'Global background border radius', "anycomment" ) )
				          ->set_description( esc_html( __( 'Global background border radius. Could be useful when you have background different then website primary color.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_GLOBAL_MARGIN )
				          ->set_title( __( 'Global margin', "anycomment" ) )
				          ->set_description( esc_html( __( 'Global margin for all comments. You may use "px", "em" or "%".', "anycomment" ) ) ),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_GLOBAL_PADDING )
				          ->set_title( __( 'Global padding', "anycomment" ) )
				          ->set_description( esc_html( __( 'Global padding for all comments. You may use "px", "em" or "%".', "anycomment" ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_GLOBAL_RADIUS )
				          ->set_title( __( 'Border radius', "anycomment" ) )
				          ->set_description( esc_html( __( 'Border radius. You may use "px", "em" or "%".', "anycomment" ) ) ),

				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_SEMI_HIDDEN_COLOR )
				          ->set_title( __( 'Semi Hidden Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Semi hidden color. This is used for dates, action links, etc.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR )
				          ->set_title( __( 'Form Fields Background', "anycomment" ) )
				          ->set_description( esc_html( __( 'Form fields background color.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_ATTACHMENT_COLOR )
				          ->set_title( __( 'Attachment Text Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Attachments text color. For example, YouTube attachments do not have previews, instead they have "YouTube" text over.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR )
				          ->set_title( __( 'Attachment Background Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Attachment background color. For example, user may attach PNG image with transparent background. This color will be used as background behind the image.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_AVATAR_RADIUS )
				          ->set_title( __( 'Avatar Border Radius', "anycomment" ) )
				          ->set_description( esc_html( __( 'Avatar border radius. You may use "px" or "%". "50%" will make avatars rounded.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_PARENT_AVATAR_SIZE )
				          ->set_title( __( 'Avatar Parent Size', "anycomment" ) )
				          ->set_description( esc_html( __( 'Parent comment avatar size.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_CHILD_AVATAR_SIZE )
				          ->set_title( __( 'Avatar Child Size', "anycomment" ) )
				          ->set_description( esc_html( __( 'Child comment avatar size. Usually, this is reply comment.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_DESIGN_BUTTON_RADIUS )
				          ->set_title( __( 'Button Radius', "anycomment" ) )
				          ->set_description( esc_html( __( 'Button border radius.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_BUTTON_COLOR )
				          ->set_title( __( 'Button Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Button text color.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR )
				          ->set_title( __( 'Button Background Color', "anycomment" ) )
				          ->set_description( esc_html( __( 'Button background color.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->color()
				          ->set_id( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE )
				          ->set_title( __( 'Button Background Color Active', "anycomment" ) )
				          ->set_description( esc_html( __( 'Button background color when hovered or focused.', "anycomment" ) ) ),
			     ] )
		);

		/**
		 * Section: Moderation
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'moderation' )
			     ->set_title( __( 'Moderation', "anycomment" ) )
			     ->set_visible( ! $isSaaSEnabled )
			     ->set_fields( [
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_MODERATE_FIRST )
				          ->set_title( __( 'Moderate First', "anycomment" ) )
				          ->set_description( esc_html( __( 'Moderators should check comment before it appears.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_MODERATE_FIRST_COMMENT_ONLY )
				          ->set_title( __( 'Moderate first comment only', 'anycomment' ) )
				          ->set_description( __( 'Moderate only first comment any further should be posted without pre moderation. When "Moderate First" option enabled, comments from users with at least one approved comment would be accepted automatically.', "anycomment" ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_LINKS_ON_HOLD )
				          ->set_title( __( 'Links on Hold', "anycomment" ) )
				          ->set_description( esc_html( __( 'Comment with links should be marked for moderation.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->textarea()
				          ->set_id( self::OPTION_MODERATE_WORDS )
				          ->set_title( __( 'Spam Words', "anycomment" ) )
				          ->set_description( esc_html( __( 'Comment should be marked for moderation when matched word from this list of comma-separated values. Also you may replace words with the folowing syntax: "word:*", where word is the matching word and after ":" is the replacement character. Example, "wor:*" would make "Hello world" as "Hello ***ld"', "anycomment" ) ) ),
			     ] )
		);

		/**
		 * Section: Notifications
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'notifications' )
			     ->set_title( __( 'Notifications', "anycomment" ) )
			     ->set_visible( ! $isSaaSEnabled )
			     ->set_fields( [
				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_NOTIFY_SENDER_NAME )
				          ->set_title( __( 'Sender Name', "anycomment" ) )
				          ->set_description( esc_html( __( 'Send name shown to email recipient. This could be your blog name.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_NOTIFY_ON_NEW_COMMENT )
				          ->set_title( __( 'Real-time updates', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show comments in real-time. Users on the page would be notified about new comments by green alert and see comment without page reload.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_INTERVAL_COMMENTS_CHECK )
				          ->set_title( __( 'New Comment Interval Checking', "anycomment" ) )
				          ->set_description( esc_html( __( 'Interval (in seconds) to check for new comments. Minimum 5 and maximum is 100 seconds.', "anycomment" ) ) ),


				     // Admin email notifications
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_NOTIFY_ADMINISTRATOR )
				          ->set_title( __( 'Notify Administrator', "anycomment" ) )
				          ->set_description( esc_html( __( 'Notify administrator via email about new comment.', "anycomment" ) ) ),

				     // Reply email notifications
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_NOTIFY_ON_NEW_REPLY )
				          ->set_title( __( 'Notify on new replies', "anycomment" ) )
				          ->set_description( esc_html( __( 'Notify users by email (if specified) about new replies. Make sure you have proper SMTP configurations in order to send emails.', "anycomment" ) ) ),


				     // Subscription email notifications
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_NOTIFY_SUBSCRIBERS )
				          ->set_title( __( 'Notify Post Subscribers', "anycomment" ) )
				          ->set_description( esc_html( __( 'Show subscription form and notify active post subscribers. Make sure you have proper SMTP configurations in order to send emails.', "anycomment" ) ) ),


				     $this->field_builder()
				          ->textarea()
				          ->set_id( self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE )
				          ->set_title( __( 'Subscription Email Template', "anycomment" ) )
				          ->set_description( esc_html( __( 'Email template for subscriptions.', "anycomment" ) ) )
				          ->set_after( function () {
					          $supportedList = [
						          '{blogName}'         => __( 'Blog name as text', 'anycomment' ),
						          '{blogUrl}'          => __( 'Blog link as text', 'anycomment' ),
						          '{blogUrlHtml}'      => __( 'Blog name in HTML link', 'anycomment' ),
						          '{postTitle}'        => __( 'Post title as text', 'anycomment' ),
						          '{postUrl}'          => __( 'Post URL as text', 'anycomment' ),
						          '{unsubscribeUrl}'   => __( 'Unsubscribe URL as text', 'anycomment' ),
						          '{postUrlHtml}'      => __( 'Post title in HTML link', 'anycomment' ),
						          '{commentText}'      => __( 'Comment text', 'anycomment' ),
						          '{commentFormatted}' => __( 'Comment text nicely formatted', 'anycomment' ),
						          '{replyUrl}'         => __( 'Reply link as text', 'anycomment' ),
						          '{replyButton}'      => __( 'Reply link as button', 'anycomment' ),
					          ];

					          $id = self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE . time();

					          $html = '<div><br><span class="button button-small" id="' . $id . '">' . __( 'More info', 'anycomment' ) . '</span><ul style="display: none;">';

					          foreach ( $supportedList as $code => $description ) {
						          $html .= sprintf( "<li>%s - %s</li>", $code, $description );
					          }

					          $html .= '</ul></div>';

					          $html .= '<script>jQuery("#' . $id . '").on("click", function() {jQuery(this).next("ul").toggle();});</script>';

					          return $html;
				          } ),


				     $this->field_builder()
				          ->textarea()
				          ->set_id( self::OPTION_NOTIFY_SUBSCRIBERS_CONFIRMATION_EMAIL_TEMPLATE )
				          ->set_title( __( 'Subscription Confirmation Email Template', "anycomment" ) )
				          ->set_description( esc_html( __( 'Email template for confirming subscription.', "anycomment" ) ) )
				          ->set_after( function () {
					          $supportedList = [
						          '{blogName}'           => __( 'Blog name as text', 'anycomment' ),
						          '{blogUrl}'            => __( 'Blog link as text', 'anycomment' ),
						          '{blogUrlHtml}'        => __( 'Blog name in HTML link', 'anycomment' ),
						          '{postTitle}'          => __( 'Post title as text', 'anycomment' ),
						          '{postUrl}'            => __( 'Post URL as text', 'anycomment' ),
						          '{postUrlHtml}'        => __( 'Post title in HTML link', 'anycomment' ),
						          '{confirmationUrl}'    => __( 'Confirmation link as text', 'anycomment' ),
						          '{confirmationButton}' => __( 'Confirmation link as button', 'anycomment' ),
					          ];

					          $id = self::OPTION_NOTIFY_SUBSCRIBERS_CONFIRMATION_EMAIL_TEMPLATE . time();

					          $html = '<div><br><span class="button button-small" id="' . $id . '">' . __( 'More info', 'anycomment' ) . '</span><ul style="display: none;">';

					          foreach ( $supportedList as $code => $description ) {
						          $html .= sprintf( "<li>%s - %s</li>", $code, $description );
					          }

					          $html .= '</ul></div>';

					          $html .= '<script>jQuery("#' . $id . '").on("click", function() {jQuery(this).next("ul").toggle();});</script>';

					          return $html;
				          } ),
			     ] )
		);


		/**
		 * Section: Files
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'files' )
			     ->set_title( __( 'Files', "anycomment" ) )
			     ->set_visible( ! $isSaaSEnabled )
			     ->set_fields( [
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_FILES_TOGGLE )
				          ->set_title( __( 'Allow File Uploads', "anycomment" ) )
				          ->set_description( esc_html( __( 'Allow to upload files.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_FILES_GUEST_CAN_UPLOAD )
				          ->set_title( __( 'File Upload By Guests', "anycomment" ) )
				          ->set_description( esc_html( __( 'Guest users can upload documents. Please be careful about this setting as some users may potentially misuse this and periodically upload unwanted files.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_FILES_SAVE_PATH )
				          ->set_title( __( 'Save directory', "anycomment" ) )
				          ->set_description( esc_html( sprintf( __( 'Absolute path where all files would be stored, including social avatars. When empty, %s would be used. Notice folder must have correct permission & owner. Notice that AnyComment would add year and month automatically to the folder you specify to limit number of files per directory.', "anycomment" ), AnyCommentUploadHandler::get_default_save_dir() ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_FILES_SAVE_SERVE_URL )
				          ->set_title( __( 'Save directory serve', "anycomment" ) )
				          ->set_description( esc_html( sprintf( __( 'URL where file can served. For example, when directory specified, it is required to specify path where image would served. When empty, %s would be used. Notice that AnyComment would add year and month automatically to the URL.', "anycomment" ), AnyCommentUploadHandler::get_default_server_dir() ) ) ),

				     $this->field_builder()
				          ->text()
				          ->set_id( self::OPTION_FILES_MIME_TYPES )
				          ->set_title( __( 'File MIME Types', "anycomment" ) )
				          ->set_description( esc_html( __( 'Comma-separated list of allowed MIME types (e.g. .png, .jpg, etc). Alternatively, you may write "image/*" for all image types or "audio/*" for audios.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_FILES_LIMIT )
				          ->set_title( __( 'File Upload Limit', "anycomment" ) )
				          ->set_description( esc_html( __( 'Maximum number of files to upload per period defined in the field below.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_FILES_LIMIT_PERIOD )
				          ->set_title( __( 'File Upload Limit Period', "anycomment" ) )
				          ->set_description( esc_html( __( 'If user will cross the limit (defined above) within specified period (in seconds) in this field, he will be give a warning.', "anycomment" ) ) ),

				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_FILES_MAX_SIZE )
				          ->set_title( __( 'File Size', "anycomment" ) )
				          ->set_description( esc_html( __( 'Maximum allowed file size in megabytes. For example, regular PNG image is about ~ 1.5-2MB, JPEG are even smaller.', "anycomment" ) ) ),
			     ] )
		);

		/**
		 * Section: Editor
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'seo' )
			     ->set_title( __( 'SEO', "anycomment" ) )
			     ->set_fields( [
				     $this->field_builder()
				          ->checkbox()
				          ->set_id( self::OPTION_SEO_TOGGLE )
				          ->set_title( __( 'Search Engine Visibility', "anycomment" ) )
				          ->set_description( esc_html( __( 'Allow search engines to discover comments.', "anycomment" ) ) ),
				     $this->field_builder()
				          ->number()
				          ->set_id( self::OPTION_SEO_DISPLAY_LIMIT )
				          ->set_title( __( 'Limit Comments', "anycomment" ) )
				          ->set_description( esc_html( __( 'Number of comments to be seen by search engines. Lower the number - faster comments would load.', "anycomment" ) ) ),
				     $this->field_builder()
				          ->set_id( self::OPTION_SEO_SORTING )
				          ->select()
				          ->set_title( __( 'Sorting', "anycomment" ) )
				          ->set_args( [
					          'options' => [
						          self::SEO_SORTING_NEW2OLD => __( 'Newest to oldest', 'anycomment' ),
						          self::SEO_SORTING_OLD2NEW => __( 'Oldest to newest', 'anycomment' ),
					          ],
				          ] )
				          ->set_description( esc_html( __( 'Choose how comments would be sorted.', "anycomment" ) ) ),
			     ] )
		);

		/**
		 * Section: Editor
		 */
		$form->add_section(
			$this->section_builder()
			     ->set_id( 'editor' )
			     ->set_title( __( 'Editor', "anycomment" ) )
			     ->set_fields( [
				     $this->field_builder()
				          ->code()
				          ->set_id( self::OPTION_EDITOR_CSS )
				          ->set_args( [ 'mode' => 'css' ] )
				          ->set_title( __( 'Custom CSS', "anycomment" ) )
				          ->set_description( esc_html( __( 'Write custom CSS, it will be only related to AnyComment. Notice: you may require to drop the cache after your changes if you have any caching plugin installed.', "anycomment" ) ) ),
			     ] )
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function run() {

		if ( AnyCommentServiceApi::is_ready() && AnyCommentIntegrationSettings::is_sass_comments_show() ) {
			echo AnyCommentTemplate::render( 'admin/saas-notice' );
		}

		$sections_html = '<form action="' . esc_url( admin_url( "admin-post.php" ) ) . '" id="' . $this->get_page_slug() . '" method="post" class="anycomment-form" novalidate>';

		$redirect_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url( $_SERVER['REQUEST_URI'] ) : '';

		$sections_html .= '<input type="hidden" name="redirect" value="' . $redirect_url . '">';
		$sections_html .= '<input type="hidden" name="action" value="' . $this->option_name . '">';
		$sections_html .= '<input type="hidden" name="nonce" value="' . wp_create_nonce( $this->option_name ) . '" />';

		$options = $this->options;

		foreach ( $options as $option ) {
			$sections = $option->get_sections();

			if ( ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					$sections_html .= $section;
				}
			} else {
				$fields = $option->get_fields();
				foreach ( $fields as $field ) {
					$sections_html .= $field;
				}
			}
		}

		$sections_html .= '<input type="submit" name="submit" id="submit" class="button button-primary" value="' . __( 'Save', 'anycomment' ) . '">';

		$sections_html .= '</form>';

		$tabs = $this->do_tab_menu();

		$html = <<<EOT
<div class="anycomment-tabs grid-x grid-margin-x">
	<aside class="cell large-5 medium-5 small-12 anycomment-tabs__menu">
	    $tabs
	</aside>
	<div class="cell auto anycomment-tabs__container">$sections_html</div>
</div>
EOT;

		return $html;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function do_tab_menu() {
		$options = $this->get_options();

		$html = '';

		foreach ( $options as $option ) {
			$html .= '<ul>';

			$sections = $option->get_sections();

			foreach ( $sections as $key => $section ) {
				$section_id = $section->get_id();

				$liClasses = ( $key == 0 ? 'current' : '' );

				$html .= '<li class="' . $liClasses . '" style="display: ' . esc_attr( $section->is_visible() ? 'block' : 'none' ) . '" data-tab="' . $section_id . '">
				<a href="#tab-' . $section_id . '">' . $section->get_title() . '</a>
				</li>';
			}
		}
		$html .= '</ul>';

		$html .= <<<EOT
        <script>
            jQuery('.anycomment-tabs__menu li').on('click', function () {
                var data = jQuery(this).attr('data-tab') || '';
                var tab_id = '#' + data;

                if (!data) {
                    return false;
                }

                jQuery('.anycomment-tabs__menu li').removeClass('current');
                jQuery('.anycomment-tabs__container__tab').removeClass('current');

                jQuery(this).addClass('current');
                jQuery(tab_id).addClass('current');

                return false;
            })
        </script>
EOT;

		return $html;
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

		$variables = [
			'global-margin'                   => AnyCommentGenericSettings::get_global_margin(),
			'global-padding'                  => AnyCommentGenericSettings::get_global_padding(),
			'global-background-color'         => AnyCommentGenericSettings::get_global_background_color(),
			'global-background-border-radius' => AnyCommentGenericSettings::get_global_background_border_radius(),
			'global-radius'                   => AnyCommentGenericSettings::get_design_global_radius(),

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
		];

		$compiler = new ScssCompiler();

		$compiledCSS = $compiler
			->set_scss( [
				$scssPath . 'app.scss',
				$scssPath . 'ReactToastify.css',
			] )
			->set_import_path( $scssPath )
			->set_variables( $variables )
			->compile();

		if ( empty( $compiledCSS ) ) {
			return false;
		}

		return $compiledCSS;
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

		$options = static::instance()->get_db_options();

		if ( ! empty( $options ) ) {
			foreach ( $options as $option_name => $option_value ) {
				if ( strpos( $option_name, '_design_' ) !== false ) {
					$items[ $option_name ] = $option_value;
				}
			}
		}

		return hash( 'sha256', serialize( $items ) );
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
		return static::instance()->get_db_option( self::OPTION_PLUGIN_TOGGLE ) !== null;
	}

	/**
	 * Check whether it is required to load comments on scroll.
	 *
	 * @return bool
	 */
	public static function is_load_on_scroll() {
		return static::instance()->get_db_option( self::OPTION_LOAD_ON_SCROLL ) !== null;
	}

	/**
	 * Check whether it is required to hold comments with links for moderation.
	 *
	 * @return bool
	 */
	public static function is_links_on_hold() {
		return static::instance()->get_db_option( self::OPTION_LINKS_ON_HOLD ) !== null;
	}

	/**
	 * Check whether it is required to mark comments for moderation.
	 *
	 * @return bool
	 */
	public static function is_moderate_first() {
		return static::instance()->get_db_option( self::OPTION_MODERATE_FIRST ) !== null;
	}

	/**
	 * Check whether it is required to moderate only first comment.
	 *
	 * @return bool
	 */
	public static function is_moderate_first_comment_only() {
		return static::instance()->get_db_option( self::OPTION_MODERATE_FIRST_COMMENT_ONLY ) !== null;
	}

	/**
	 * Check whether it is required to show video attachments.
	 *
	 * @return bool
	 */
	public static function is_show_twitter_embeds() {
		return static::instance()->get_db_option( self::OPTION_SHOW_TWITTER_EMBEDS ) !== null;
	}

	/**
	 * Check whether it is required to show video attachments.
	 *
	 * @return bool
	 */
	public static function is_show_video_attachments() {
		return static::instance()->get_db_option( self::OPTION_SHOW_VIDEO_ATTACHMENTS ) !== null;
	}

	/**
	 * Check whether it is required to show image attachments.
	 *
	 * @return bool
	 */
	public static function is_show_image_attachments() {
		return static::instance()->get_db_option( self::OPTION_SHOW_IMAGE_ATTACHMENTS ) !== null;
	}

	/**
	 * Check whether it is required to make links clickable.
	 *
	 * @return bool
	 */
	public static function is_link_clickable() {
		return static::instance()->get_db_option( self::OPTION_MAKE_LINKS_CLICKABLE ) !== null;
	}

	/**
	 * Check whether it is required to show social profile URL or not.
	 *
	 * @return bool
	 */
	public static function is_show_profile_url() {
		return static::instance()->get_db_option( self::OPTION_SHOW_PROFILE_URL ) !== null;
	}

	/**
	 * Check whether it is required to show list of social icons in the login page.
	 *
	 * @return bool
	 */
	public static function is_show_socials_in_login_page() {
		return static::instance()->get_db_option( self::OPTION_SHOW_SOCIALS_IN_LOGIN_PAGE ) !== null;
	}

	/**
	 * Check whether it is required to show admin bar.
	 *
	 * @return bool
	 */
	public static function is_show_admin_bar() {
		return static::instance()->get_db_option( self::OPTION_SHOW_ADMIN_BAR ) !== null;
	}

	/**
	 * Check whether it is required to notify with alert on new comment.
	 *
	 * @return bool
	 */
	public static function is_notify_on_new_comment() {
		return static::instance()->get_db_option( self::OPTION_NOTIFY_ON_NEW_COMMENT ) !== null;
	}

	/**
	 * Check whether it is required to notify administrator about new comment.
	 *
	 * @return bool
	 */
	public static function is_notify_admin() {
		return static::instance()->get_db_option( self::OPTION_NOTIFY_ADMINISTRATOR ) !== null;
	}

	/**
	 * Check whether it is required to notify subscribers about new comment(s).
	 *
	 * @return bool
	 */
	public static function is_notify_subscribers() {
		return static::instance()->get_db_option( self::OPTION_NOTIFY_SUBSCRIBERS ) !== null;
	}

	/**
	 * Get sender name. When name is empty, `blogname` options will be returned.
	 * @return string
	 */
	public static function get_notify_email_sender_name() {
		$value = static::instance()->get_db_option( self::OPTION_NOTIFY_SENDER_NAME );

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
		return static::instance()->get_db_option( self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE );
	}

	/**
	 * Get subscribers confirmation email template format.
	 *
	 * @return string|null
	 */
	public static function get_notify_email_subscribers_confirmation_template() {
		return static::instance()->get_db_option( self::OPTION_NOTIFY_SUBSCRIBERS_CONFIRMATION_EMAIL_TEMPLATE );
	}

	/**
	 * Check whether it is required to notify by sending email on new reply.
	 *
	 * @return bool
	 */
	public static function is_notify_on_new_reply() {
		return static::instance()->get_db_option( self::OPTION_NOTIFY_ON_NEW_REPLY ) !== null;
	}

	/**
	 * Get list of words to moderate.
	 *
	 * @return string|null
	 */
	public static function get_moderate_words() {
		return static::instance()->get_db_option( self::OPTION_MODERATE_WORDS );
	}

	/**
	 * Check whether file upload is allowed.
	 *
	 * @return bool
	 */
	public static function is_file_upload_allowed() {
		return static::instance()->get_db_option( self::OPTION_FILES_TOGGLE ) !== null;
	}

	/**
	 * Check whether guests uses can upload files.
	 *
	 * @return bool
	 */
	public static function is_guest_can_upload() {
		return static::instance()->get_db_option( self::OPTION_FILES_GUEST_CAN_UPLOAD );
	}

	/**
	 * Get file max size.
	 *
	 * @return float|null
	 */
	public static function get_file_max_size() {
		return static::instance()->get_db_option( self::OPTION_FILES_MAX_SIZE );
	}

	/**
	 * Get files save path.
	 *
	 * @return string|null
	 */
	public static function get_file_save_path() {
		$path = static::instance()->get_db_option( self::OPTION_FILES_SAVE_PATH );

		if ( empty( $path ) ) {
			return null;
		}

		$path = rtrim( $path, '/\\' );

		$ds = DIRECTORY_SEPARATOR;

		$path .= $ds . date( 'Y' ) . $ds . date( 'm' );

		if ( ! is_dir( $path ) ) {
			@mkdir( $path, 0755, true );

			if ( ! is_dir( $path ) ) {
				return null;
			}
		}

		return $path;
	}

	/**
	 * Get files save URL.
	 *
	 * @return string|null
	 */
	public static function get_file_save_serve_url() {
		$url = static::instance()->get_db_option( self::OPTION_FILES_SAVE_SERVE_URL );

		if ( empty( $url ) ) {
			return null;
		}

		$url = rtrim( $url, '/' );

		$url .= '/' . date( 'Y' ) . '/' . date( 'm' );

		return $url;
	}


	/**
	 * Get file upload limit.
	 *
	 * @return float|null
	 */
	public static function get_file_limit() {
		return static::instance()->get_db_option( self::OPTION_FILES_LIMIT );
	}

	/**
	 * Get file upload period limit in seconds.
	 *
	 * @return int|null
	 */
	public static function get_file_upload_limit() {
		return static::instance()->get_db_option( self::OPTION_FILES_LIMIT_PERIOD );
	}

	/**
	 * Get allowed file MIME types.
	 *
	 * @return string|null
	 */
	public static function get_file_mime_types() {
		return static::instance()->get_db_option( self::OPTION_FILES_MIME_TYPES );
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
			if ( substr( $validType, 0, 1 ) === '.' ) {
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
		$is_toolbar_on                 = static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_TOGGLE ) !== null;
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
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_BOLD ) !== null;
	}

	/**
	 * Check whether italic option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_italic() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_ITALIC ) !== null;
	}

	/**
	 * Check whether underline option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_underline() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_UNDERLINE ) !== null;
	}

	/**
	 * Check whether blockquote option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_blockquote() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_QUOTE ) !== null;
	}

	/**
	 * Check whether ordered list option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_ordered_list() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_ORDERED ) !== null;
	}

	/**
	 * Check whether bullet list option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_bullet_list() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_BULLET ) !== null;
	}

	/**
	 * Check whether link option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_link() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_LINK ) !== null;
	}

	/**
	 * Check whether clean option should be seen in editor toolbar.
	 *
	 * @return bool
	 */
	public static function is_editor_toolbar_clean() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_TOOLBAR_CLEAN ) !== null;
	}


	/**
	 * Enable custom design.
	 *
	 * @return bool
	 */
	public static function is_design_custom() {
		return static::instance()->get_db_option( self::OPTION_DESIGN_CUSTOM_TOGGLE ) !== null;
	}

	/**
	 * Get global background color.
	 *
	 * @return string
	 */
	public static function get_global_background_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_GLOBAL_BACKGROUND_COLOR ) );
	}

	/**
	 * Get global background border radius.
	 *
	 * @return string
	 */
	public static function get_global_background_border_radius() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS ) );
	}

	/**
	 * Get global margin.
	 *
	 * @return string
	 */
	public static function get_global_margin() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_GLOBAL_MARGIN ) );
	}

	/**
	 * Get global padding.
	 *
	 * @return string
	 */
	public static function get_global_padding() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_GLOBAL_PADDING ) );
	}

	/**
	 * Get design font size.
	 *
	 * @return string|null
	 */
	public static function get_design_font_size() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_FONT_SIZE ) );
	}

	/**
	 * Get design font family size.
	 *
	 * @return string|null
	 */
	public static function get_design_font_family() {
		return static::instance()->get_db_option( self::OPTION_DESIGN_FONT_FAMILY );
	}

	/**
	 * Get design semi hidden color.
	 *
	 * @return string|null
	 */
	public static function get_design_semi_hidden_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_SEMI_HIDDEN_COLOR ) );
	}


	/**
	 * Get link color.
	 *
	 * @return string|null
	 */
	public static function get_design_link_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_LINK_COLOR ) );
	}

	/**
	 * Get text color.
	 *
	 * @return string|null
	 */
	public static function get_design_text_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_TEXT_COLOR ) );
	}

	/**
	 * Get design form field background color.
	 *
	 * @return string|null
	 */
	public static function get_design_form_field_background_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR ) );
	}

	/**
	 * Get design attachment color.
	 *
	 * @return string|null
	 */
	public static function get_design_attachment_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_ATTACHMENT_COLOR ) );
	}

	/**
	 * Get design attachment background color.
	 *
	 * @return string|null
	 */
	public static function get_design_attachment_background_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR ) );
	}

	/**
	 * Get design avatar border radius.
	 *
	 * @return string|null
	 */
	public static function get_design_avatar_radius() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_AVATAR_RADIUS ) );
	}

	/**
	 * Get design parent avatar size.
	 *
	 * @return string|null
	 */
	public static function get_design_parent_avatar_size() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_PARENT_AVATAR_SIZE ) );
	}

	/**
	 * Get design child avatar size.
	 *
	 * @return string|null
	 */
	public static function get_design_child_avatar_size() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_CHILD_AVATAR_SIZE ) );
	}

	/**
	 * Get design button color.
	 *
	 * @return string|null
	 */
	public static function get_design_button_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_BUTTON_COLOR ) );
	}

	/**
	 * Get design button background color.
	 *
	 * @return string|null
	 */
	public static function get_design_button_background_color() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR ) );
	}

	/**
	 * Get design button background color color.
	 *
	 * @return string|null
	 */
	public static function get_design_button_background_color_active() {
		return AnyCommentInputHelper::normalize_hex_color( static::instance()->get_db_option( self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE ) );
	}

	/**
	 * Get design button radius.
	 *
	 * @return string|null
	 */
	public static function get_design_button_radius() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_BUTTON_RADIUS ) );
	}

	/**
	 * Get design button radius.
	 *
	 * @return string|null
	 */
	public static function get_design_global_radius() {
		return AnyCommentInputHelper::normalize_css_size( static::instance()->get_db_option( self::OPTION_DESIGN_GLOBAL_RADIUS ) );
	}

	/**
	 * Get interval in seconds per each check for new comments.
	 *
	 * @return string
	 * @see AnyCommentGenericSettings::is_notify_on_new_reply() for more information. Which option is ignored when notification disabled.
	 *
	 */
	public static function get_interval_comments_check() {
		$intervalInSeconds = static::instance()->get_db_option( self::OPTION_INTERVAL_COMMENTS_CHECK );

		if ( $intervalInSeconds < 5 ) {
			$intervalInSeconds = 5;
		} elseif ( $intervalInSeconds > 100 ) {
			$intervalInSeconds = 100;
		}

		return $intervalInSeconds;
	}


	/**
	 * Get default date & time format.
	 *
	 * Notice: default value is relative when nothing is defined.
	 *
	 * @return mixed|null
	 */
	public static function get_datetime_format() {
		$value = static::instance()->get_db_option( self::OPTION_COMMENT_DATETIME_FORMAT );

		if ( $value !== self::DATETIME_FORMAT_NATIVE && $value !== self::DATETIME_FORMAT_RELATIVE ) {
			return self::DATETIME_FORMAT_RELATIVE;
		}

		return $value;
	}

	/**
	 * Get default group for registered user.
	 *
	 * @return string
	 */
	public static function get_register_default_group() {
		return static::instance()->get_db_option( self::OPTION_REGISTER_DEFAULT_GROUP );
	}

	/**
	 * Get user agreement link. Used when user is guest and be authorizing using social network.
	 *
	 * @return string|null
	 */
	public static function get_user_agreement_link() {
		return static::instance()->get_db_option( self::OPTION_USER_AGREEMENT_LINK );
	}

	/**
	 * Check whether read more should be shown.
	 *
	 * @return bool
	 */
	public static function is_read_more_on() {
		return static::instance()->get_db_option( self::OPTION_READ_MORE_TOGGLE ) !== null;
	}

	/**
	 * Check whether rating should be displayed or not.
	 *
	 * @return bool
	 */
	public static function is_rating_on() {
		return static::instance()->get_db_option( self::OPTION_RATING_TOGGLE ) !== null;
	}

	/**
	 * Get comment loaded per page setting value.
	 *
	 * @return int
	 */
	public static function get_per_page() {
		$value = (int) static::instance()->get_db_option( self::OPTION_COUNT_PER_PAGE );

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
		$value = (int) static::instance()->get_db_option( self::OPTION_COMMENT_UPDATE_TIME );

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
		$value = static::instance()->get_db_option( self::OPTION_DEFAULT_SORT_BY );

		if ( $value !== self::SORT_DESC && $value !== self::SORT_ASC ) {
			return self::SORT_DESC;
		}

		return $value;
	}


	/**
	 * Get comment rating type.
	 *
	 * @return string
	 */
	public static function get_comment_rating() {
		$value = static::instance()->get_db_option( self::OPTION_COMMENT_RATING );

		if ( $value !== self::COMMENT_RATING_LIKES && $value !== self::COMMENT_RATING_LIKES_DISLIKES ) {
			return self::COMMENT_RATING_LIKES;
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
		$value = static::instance()->get_db_option( self::OPTION_DEFAULT_AVATAR );

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
		return static::instance()->get_db_option( self::OPTION_FORM_TYPE );
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
		$value    = $instance->get_db_option( self::OPTION_GUEST_FIELDS );

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
	 * Check whether comment was updated or not.
	 *
	 * @return bool
	 */
	public static function is_show_updated_info() {
		return static::instance()->get_db_option( self::OPTION_SHOW_UPDATED_INFO ) !== null;
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
	 * Get custom CSS style to be rendered on frontend.
	 *
	 * @return mixed|null
	 */
	public static function get_editor_css() {
		return static::instance()->get_db_option( self::OPTION_EDITOR_CSS );
	}

	/**
	 * Check whether SEO option is on or not.
	 *
	 * @return bool
	 */
	public static function is_seo_on() {
		return static::instance()->get_db_option( self::OPTION_SEO_TOGGLE ) !== null;
	}

	/**
	 * Get SEO limit.
	 *
	 * @return bool
	 */
	public static function get_seo_limit() {
		$limit = static::instance()->get_db_option( self::OPTION_SEO_DISPLAY_LIMIT );

		if ( empty( $limit ) ) {
			$limit = 50;
		}

		return $limit;
	}

	/**
	 * Get SEO sorting.
	 *
	 * @return bool
	 */
	public static function get_seo_sorting() {
		$sorting = static::instance()->get_db_option( self::OPTION_SEO_SORTING );

		if ( ! empty( $sorting ) && $sorting !== self::SEO_SORTING_OLD2NEW && $sorting !== self::SEO_SORTING_NEW2OLD ) {
			$sorting = self::SEO_SORTING_NEW2OLD;
		}

		return $sorting;
	}

	/**
	 * Check whether copyright should on or not.
	 *
	 * @return bool
	 */
	public static function is_copyright_on() {
		return static::instance()->get_db_option( self::OPTION_COPYRIGHT_TOGGLE ) !== null;
	}

	/**
	 * @inheritDoc
	 */
	protected function save_option_keys() {
		return [
			self::OPTION_NOTIFY_SENDER_NAME,
			self::OPTION_NOTIFY_ON_NEW_COMMENT,
			self::OPTION_NOTIFY_SUBSCRIBERS,
			self::OPTION_NOTIFY_ON_NEW_REPLY,
			self::OPTION_NOTIFY_ADMINISTRATOR,
			self::OPTION_NOTIFY_SUBSCRIBERS_EMAIL_TEMPLATE,
			self::OPTION_NOTIFY_SUBSCRIBERS_CONFIRMATION_EMAIL_TEMPLATE,
			self::OPTION_PLUGIN_TOGGLE,
			self::OPTION_DEFAULT_SORT_BY,
			self::OPTION_COMMENT_RATING,
			self::OPTION_DEFAULT_AVATAR,
			self::OPTION_EDITOR_TOOLBAR_TOGGLE,
			self::OPTION_EDITOR_TOOLBAR_BOLD,
			self::OPTION_EDITOR_TOOLBAR_ITALIC,
			self::OPTION_EDITOR_TOOLBAR_UNDERLINE,
			self::OPTION_EDITOR_TOOLBAR_QUOTE,
			self::OPTION_EDITOR_TOOLBAR_ORDERED,
			self::OPTION_EDITOR_TOOLBAR_BULLET,
			self::OPTION_EDITOR_TOOLBAR_LINK,
			self::OPTION_EDITOR_TOOLBAR_CLEAN,
			self::OPTION_COMMENT_DATETIME_FORMAT,
			self::OPTION_REGISTER_DEFAULT_GROUP,
			self::OPTION_INTERVAL_COMMENTS_CHECK,
			# General
			self::OPTION_READ_MORE_TOGGLE,
			self::OPTION_RATING_TOGGLE,
			self::OPTION_COUNT_PER_PAGE,
			self::OPTION_COMMENT_UPDATE_TIME,
			self::OPTION_USER_AGREEMENT_LINK,
			self::OPTION_LOAD_ON_SCROLL,
			self::OPTION_MODERATE_FIRST_COMMENT_ONLY,
			self::OPTION_MODERATE_FIRST,
			self::OPTION_MODERATE_WORDS,
			self::OPTION_LINKS_ON_HOLD,
			self::OPTION_SHOW_SOCIALS_IN_LOGIN_PAGE,
			self::OPTION_SHOW_ADMIN_BAR,
			self::OPTION_SHOW_PROFILE_URL,
			self::OPTION_SHOW_TWITTER_EMBEDS,
			self::OPTION_SHOW_VIDEO_ATTACHMENTS,
			self::OPTION_SHOW_IMAGE_ATTACHMENTS,
			self::OPTION_MAKE_LINKS_CLICKABLE,
			self::OPTION_COPYRIGHT_TOGGLE,
			#File upload
			self::OPTION_FILES_TOGGLE,
			self::OPTION_FILES_GUEST_CAN_UPLOAD,
			self::OPTION_FILES_MIME_TYPES,
			self::OPTION_FILES_LIMIT,
			self::OPTION_FILES_LIMIT_PERIOD,
			self::OPTION_FILES_MAX_SIZE,
			self::OPTION_FILES_SAVE_PATH,
			self::OPTION_FILES_SAVE_SERVE_URL,
			#Design
			self::OPTION_FORM_TYPE,
			self::OPTION_GUEST_FIELDS,
			self::OPTION_SHOW_UPDATED_INFO,
			self::OPTION_DESIGN_CUSTOM_TOGGLE,
			self::OPTION_DESIGN_GLOBAL_PADDING,
			self::OPTION_DESIGN_GLOBAL_MARGIN,
			self::OPTION_DESIGN_GLOBAL_BACKGROUND_BORDER_RADIUS,
			self::OPTION_DESIGN_GLOBAL_BACKGROUND_COLOR,
			self::OPTION_DESIGN_FONT_SIZE,
			self::OPTION_DESIGN_FONT_FAMILY,
			self::OPTION_DESIGN_SEMI_HIDDEN_COLOR,
			self::OPTION_DESIGN_LINK_COLOR,
			self::OPTION_DESIGN_TEXT_COLOR,
			self::OPTION_DESIGN_FORM_FIELD_BACKGROUND_COLOR,
			self::OPTION_DESIGN_ATTACHMENT_COLOR,
			self::OPTION_DESIGN_ATTACHMENT_BACKGROUND_COLOR,
			self::OPTION_DESIGN_AVATAR_RADIUS,
			self::OPTION_DESIGN_PARENT_AVATAR_SIZE,
			self::OPTION_DESIGN_CHILD_AVATAR_SIZE,
			self::OPTION_DESIGN_BUTTON_COLOR,
			self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR,
			self::OPTION_DESIGN_BUTTON_BACKGROUND_COLOR_ACTIVE,
			self::OPTION_DESIGN_BUTTON_RADIUS,
			self::OPTION_DESIGN_GLOBAL_RADIUS,
			# Editor
			self::OPTION_EDITOR_CSS,
			# SEO
			self::OPTION_SEO_TOGGLE,
			self::OPTION_SEO_DISPLAY_LIMIT,
			self::OPTION_SEO_SORTING,
		];
	}
}
