<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentSocialSettings' ) ) :
	/**
	 * AnyCommentAdminPages helps to process website authentication.
	 */
	class AnyCommentSocialSettings extends AnyCommentAdminOptions {
		/**
		 * @inheritdoc
		 */
		protected $option_group = 'anycomment-social-group';
		/**
		 * @inheritdoc
		 */
		protected $option_name = 'anycomment-social';
		/**
		 * @inheritdoc
		 */
		protected $page_slug = 'anycomment-settings-social';

		/**
		 * VK Options
		 */
		const OPTION_VK_TOGGLE = 'social_vk_toggle_field';
		const OPTION_VK_APP_ID = 'social_vk_app_id_field';
		const OPTION_VK_SECRET = 'social_vk_app_secret_field';

		/**
		 * Twitter options
		 */
		const OPTION_TWITTER_TOGGLE = 'social_twitter_toggle_field';
		const OPTION_TWITTER_CONSUMER_KEY = 'social_twitter_consumer_key_field';
		const OPTION_TWITTER_CONSUMER_SECRET = 'social_twitter_consumer_secret_field';

		/**
		 * Facebook Options
		 */
		const OPTION_FACEBOOK_TOGGLE = 'social_facebook_toggle_field';
		const OPTION_FACEBOOK_APP_ID = 'social_facebook_app_id_field';
		const OPTION_FACEBOOK_APP_SECRET = 'social_facebook_app_secret_field';

		/**
		 * Google Options
		 */
		const OPTION_GOOGLE_TOGGLE = 'social_google_toggle_field';
		const OPTION_GOOGLE_CLIENT_ID = 'social_google_client_id_field';
		const OPTION_GOOGLE_SECRET = 'social_google_secret_field';

		/**
		 * Github Options
		 */
		const OPTION_GITHUB_TOGGLE = 'social_github_toggle_field';
		const OPTION_GITHUB_CLIENT_ID = 'social_github_app_id_field';
		const OPTION_GITHUB_SECRET = 'social_github_app_secret_field';

		/**
		 * Odnoklassniki Options
		 */
		const OPTION_OK_TOGGLE = 'social_odnoklassniki_toggle_field';
		const OPTION_OK_APP_ID = 'social_odnoklassniki_app_id_field';
		const OPTION_OK_APP_KEY = 'social_odnoklassniki_app_key_field';
		const OPTION_OK_APP_SECRET = 'social_odnoklassniki_app_secret_field';


		/**
		 * AC_SocialSettingPage constructor.
		 *
		 * @param bool $init If required to init the model.
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
			add_action( 'admin_menu', [ $this, 'init_submenu' ] );
			add_action( 'admin_init', [ $this, 'init_settings' ] );
		}

		/**
		 * {@inheritdoc}
		 */
		public function init_submenu() {
			add_submenu_page(
				'anycomment-dashboard',
				__( 'Social Settings', "anycomment" ),
				__( 'Social Settings', "anycomment" ),
				'manage_options',
				$this->page_slug,
				[ $this, 'page_html' ]
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function init_settings() {
			/**
			 * VK
			 */
			add_settings_section(
				'section_vk',
				__( 'VK', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'VK authorization settings.', "anycomment" ) ?></p>

                    <table class="form-table">
                        <tr>
                            <th><label for="vk-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="vk-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_vk_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_vk',
				[
					[
						'id'          => self::OPTION_VK_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => esc_html( __( 'Allow VK authorization', "anycomment" ) )
					],
					[
						'id'          => self::OPTION_VK_APP_ID,
						'title'       => __( 'Application ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter app id. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment" ), 'https://vk.com/apps?act=manage' )
					],
					[
						'id'          => self::OPTION_VK_SECRET,
						'title'       => __( 'Secure key', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter secure key. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment" ), 'https://vk.com/apps?act=manage' )
					]
				]
			);

			/**
			 * Twitter
			 */
			add_settings_section(
				'section_twitter',
				__( 'Twitter', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Twitter authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="twitter-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="twitter-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_twitter_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_twitter',
				[
					[
						'id'          => self::OPTION_TWITTER_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Twitter authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_TWITTER_CONSUMER_KEY,
						'title'       => __( 'Consumer Key', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter consumer key. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://apps.twitter.com/' )
					],
					[
						'id'          => self::OPTION_TWITTER_CONSUMER_SECRET,
						'title'       => __( 'Consumer Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter consumer secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://apps.twitter.com/' )
					]
				]
			);

			/**
			 * Facebook
			 */
			add_settings_section(
				'section_facebook',
				__( 'Facebook', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Facebook authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="facebook-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="facebook-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_facebook_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_facebook',
				[
					[
						'id'          => self::OPTION_FACEBOOK_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Facebook authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_FACEBOOK_APP_ID,
						'title'       => __( 'App ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter app id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://developers.facebook.com/apps/' )
					],
					[
						'id'          => self::OPTION_FACEBOOK_APP_SECRET,
						'title'       => __( 'App Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter app secret. Can be found in the list of <a href="%" target="_blank">apps</a>', "anycomment" ), 'https://developers.facebook.com/apps/' )
					]
				]
			);

			/**
			 * Google
			 */
			add_settings_section(
				'section_google',
				__( 'Google', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Google authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="google-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="google-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_google_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_google',
				[
					[
						'id'          => self::OPTION_GOOGLE_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Google authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_GOOGLE_CLIENT_ID,
						'title'       => __( 'Client ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://console.developers.google.com/apis/credentials' )
					],
					[
						'id'          => self::OPTION_GOOGLE_SECRET,
						'title'       => __( 'Client Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://console.developers.google.com/apis/credentials' )
					]
				]
			);

			/**
			 * GitHub
			 */
			add_settings_section(
				'section_github',
				__( 'Github', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Github authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="github-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="github-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_github_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_github',
				[
					[
						'id'          => self::OPTION_GITHUB_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow GitHub authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_GITHUB_CLIENT_ID,
						'title'       => __( 'Client ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://github.com/settings/developers' )
					],
					[
						'id'          => self::OPTION_GITHUB_SECRET,
						'title'       => __( 'Client Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://github.com/settings/developers' )
					]
				]
			);

			/**
			 * GitHub
			 */
			add_settings_section(
				'section_odnoklassniki',
				__( 'Odnoklassniki', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Odnoklassniki authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="odnoklassniki-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            </th>
                            <td><input type="text" id="odnoklassniki-callback" onclick="this.select()"
                                       readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_ok_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_odnoklassniki',
				[
					[
						'id'          => self::OPTION_OK_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Odnoklassniki authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_OK_APP_ID,
						'title'       => __( 'App ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => __( 'Enter app id. Can be found in the email sent to you by Odnoklassniki', "anycomment" ),
					],
					[
						'id'          => self::OPTION_OK_APP_KEY,
						'title'       => __( 'App Key', "anycomment" ),
						'callback'    => 'input_text',
						'description' => __( 'Enter app key. Can be found in the email sent to you by Odnoklassniki', "anycomment" ),
					],
					[
						'id'          => self::OPTION_OK_APP_SECRET,
						'title'       => __( 'App Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => __( 'Enter client secret. Can be found in the email sent to you by Odnoklassniki', "anycomment" ),
					]
				]
			);
		}

		/**
		 * Check if user enter at least some information about socials.
		 *
		 * @return bool
		 */
		public static function hasAnySocial() {
			return static::instance()->hasOptions();
		}

		/**
		 * Check whether VK social is on.
		 *
		 * @return bool
		 */
		public static function isVkOn() {
			return static::instance()->getOption( self::OPTION_VK_TOGGLE ) !== null;
		}

		/**
		 * Get VK App ID.
		 *
		 * @return int|null
		 */
		public static function getVkAppId() {
			return static::instance()->getOption( self::OPTION_VK_APP_ID );
		}

		/**
		 * Get VK Secure key.
		 *
		 * @return string|null
		 */
		public static function getVkSecureKey() {
			return static::instance()->getOption( self::OPTION_VK_SECRET );
		}

		/**
		 * Check whether GitHub social is on.
		 *
		 * @return bool
		 */
		public static function isGithubOn() {
			return static::instance()->getOption( self::OPTION_GITHUB_TOGGLE ) !== null;
		}

		/**
		 * Get GitHub client ID.
		 *
		 * @return int|null
		 */
		public static function getGithubClientId() {
			return static::instance()->getOption( self::OPTION_GITHUB_CLIENT_ID );
		}

		/**
		 * Get GitHub secret key.
		 *
		 * @return string|null
		 */
		public static function getGithubSecretKey() {
			return static::instance()->getOption( self::OPTION_GITHUB_SECRET );
		}

		/**
		 * Check whether Twitter is on.
		 *
		 * @return bool
		 */
		public static function isTwitterOn() {
			return static::instance()->getOption( self::OPTION_TWITTER_TOGGLE ) !== null;
		}

		/**
		 * Get Twitter consumer key.
		 *
		 * @return string|null
		 */
		public static function getTwitterConsumerKey() {
			return static::instance()->getOption( self::OPTION_TWITTER_CONSUMER_KEY );
		}

		/**
		 * Get Twitter consumer secret.
		 *
		 * @return string|null
		 */
		public static function getTwitterConsumerSecret() {
			return static::instance()->getOption( self::OPTION_TWITTER_CONSUMER_SECRET );
		}

		/**
		 * Check whether Facebook social is on.
		 *
		 * @return bool
		 */
		public static function isFbOn() {
			return static::instance()->getOption( self::OPTION_FACEBOOK_TOGGLE ) !== null;
		}

		/**
		 * Get Facebook App ID.
		 *
		 * @return int|null
		 */
		public static function getFbAppId() {
			return static::instance()->getOption( self::OPTION_FACEBOOK_APP_ID );
		}

		/**
		 * Get Facebook Secure key.
		 *
		 * @return string|null
		 */
		public static function getFbAppSecret() {
			return static::instance()->getOption( self::OPTION_FACEBOOK_APP_SECRET );
		}

		/**
		 * Check whether Google social is on.
		 *
		 * @return bool
		 */
		public static function isGoogleOn() {
			return static::instance()->getOption( self::OPTION_GOOGLE_TOGGLE ) !== null;
		}

		/**
		 * Get Google Client ID.
		 *
		 * @return int|null
		 */
		public static function getGoogleClientId() {
			return static::instance()->getOption( self::OPTION_GOOGLE_CLIENT_ID );
		}

		/**
		 * Get Google secret key.
		 *
		 * @return string|null
		 */
		public static function getGoogleSecret() {
			return static::instance()->getOption( self::OPTION_GOOGLE_SECRET );
		}

		/**
		 * Check whether Odnoklassniki social is on.
		 *
		 * @return bool
		 */
		public static function isOkOn() {
			return static::instance()->getOption( self::OPTION_OK_TOGGLE ) !== null;
		}

		/**
		 * Get Odnoklassniki app ID.
		 *
		 * @return int|null
		 */
		public static function getOkAppId() {
			return static::instance()->getOption( self::OPTION_OK_APP_ID );
		}

		/**
		 * Get Odnoklassniki app key.
		 *
		 * @return int|null
		 */
		public static function getOkAppKey() {
			return static::instance()->getOption( self::OPTION_OK_APP_KEY );
		}

		/**
		 * Get Odnoklassniki app secret key.
		 *
		 * @return string|null
		 */
		public static function getOkAppSecret() {
			return static::instance()->getOption( self::OPTION_OK_APP_SECRET );
		}
	}
endif;

