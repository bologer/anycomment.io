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
		const OPTION_VK_TOGGLE = 'social_vkontakte_toggle_field';
		const OPTION_VK_APP_ID = 'social_vkontakte_app_id_field';
		const OPTION_VK_SECRET = 'social_vkontakte_app_secret_field';

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
		 * Instagram
		 */
		const OPTION_INSTAGRAM_TOGGLE = 'social_instagram_toggle_field';
		const OPTION_INSTAGRAM_CLIENT_ID = 'social_instagram_client_id_field';
		const OPTION_INSTAGRAM_CLIENT_SECRET = 'social_instagram_client_secret_field';

		/**
		 * Twitch
		 */
		const OPTION_TWITCH_TOGGLE = 'social_twitch_toggle_field';
		const OPTION_TWITCH_CLIENT_ID = 'social_twitch_client_id_field';
		const OPTION_TWITCH_CLIENT_SECRET = 'social_twitch_client_secret_field';

		/**
		 * Dribbble
		 */
		const OPTION_DRIBBBLE_TOGGLE = 'social_dribbble_toggle_field';
		const OPTION_DRIBBBLE_CLIENT_ID = 'social_dribbble_client_id_field';
		const OPTION_DRIBBBLE_CLIENT_SECRET = 'social_dribbble_client_secret_field';

		/**
		 * Yahoo
		 */
		const OPTION_YAHOO_TOGGLE = 'social_yahoo_toggle_field';
		const OPTION_YAHOO_APP_ID = 'social_yahoo_app_id_field';
		const OPTION_YAHOO_CLIENT_SECRET = 'social_yahoo_client_secret_field';

		/**
		 * WordPress.
		 */
		const OPTION_WORDPRESS_NATIVE_TOGGLE = 'social_wordpress_toggle_field';


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
			add_action( 'admin_init', [ $this, 'init_settings' ] );
		}

		/**
		 * {@inheritdoc}
		 */
		public function init_settings() {
			/**
			 * VK
			 */
			add_settings_section(
				'section_vkontakte',
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
				'section_vkontakte',
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

			/**
			 * Instagram
			 */
			add_settings_section(
				'section_instagram',
				__( 'Instagram', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Instagram authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="instagram-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="instagram-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_instagram_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_instagram',
				[
					[
						'id'          => self::OPTION_INSTAGRAM_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Instagram authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_INSTAGRAM_CLIENT_ID,
						'title'       => __( 'Client ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client id. Can be found in <a href="%s" target="_blank">Manage Clients</a>', "anycomment" ), 'https://www.instagram.com/developer/clients/manage/' )
					],
					[
						'id'          => self::OPTION_INSTAGRAM_CLIENT_SECRET,
						'title'       => __( 'Client Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client secret. Can be found in <a href="%s" target="_blank">Manage Clients</a>', "anycomment" ), 'https://www.instagram.com/developer/clients/manage/' )
					]
				]
			);


			/**
			 * Twitch
			 */
			add_settings_section(
				'section_twitch',
				__( 'Twitch', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Twitch authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="twitch-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="twitch-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_twitch_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_twitch',
				[
					[
						'id'          => self::OPTION_TWITCH_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Twitch authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_TWITCH_CLIENT_ID,
						'title'       => __( 'Client ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client id. It can be found in the <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://glass.twitch.tv/console/apps' )
					],
					[
						'id'          => self::OPTION_TWITCH_CLIENT_SECRET,
						'title'       => __( 'Client Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://glass.twitch.tv/console/apps' )
					]
				]
			);

			/**
			 * Dribble
			 */
			add_settings_section(
				'section_dribbble',
				__( 'Dribbble', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Dribbble authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="dribbble-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="dribbble-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_dribbble_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_dribbble',
				[
					[
						'id'          => self::OPTION_DRIBBBLE_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Dribbble authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_DRIBBBLE_CLIENT_ID,
						'title'       => __( 'Client ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client id. It can be found in the <a href="%s" target="_blank">your applications</a>', "anycomment" ), 'https://dribbble.com/account/applications' )
					],
					[
						'id'          => self::OPTION_DRIBBBLE_CLIENT_SECRET,
						'title'       => __( 'Client Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">your applications</a>', "anycomment" ), 'https://dribbble.com/account/applications' )
					]
				]
			);

			/**
			 * Yahoo
			 */
			add_settings_section(
				'section_yahoo',
				__( 'Yahoo', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'Yahoo authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="yahoo-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="yahoo-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_yahoo_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug . time(),
				'section_yahoo',
				[
					[
						'id'          => self::OPTION_YAHOO_TOGGLE,
						'title'       => __( 'Enable', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow Yahoo authorization', "anycomment" )
					],
					[
						'id'          => self::OPTION_YAHOO_APP_ID,
						'title'       => __( 'App ID', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter app id. It can be found in the <a href="%s" target="_blank">my apps</a>', "anycomment" ), 'https://developer.yahoo.com/apps/' )
					],
					[
						'id'          => self::OPTION_YAHOO_CLIENT_SECRET,
						'title'       => __( 'Client Secret', "anycomment" ),
						'callback'    => 'input_text',
						'description' => sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">my apps</a>', "anycomment" ), 'https://developer.yahoo.com/apps/' )
					]
				]
			);


			/**
			 * WordPress
			 */
			add_settings_section(
				'section_wordpress',
				__( 'WordPress', "anycomment" ),
				function () {
					?>
                    <p><?= __( 'WordPress authorization settings.', "anycomment" ) ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="yahoo-wordpress"><?= __( 'Callback URL', 'anycomment' ) ?></label></th>
                            <td><input type="text" id="yahoo-callback" onclick="this.select()" readonly="readonly"
                                       value="<?= AnyCommentSocialAuth::get_wordpress_callback() ?>"></td>
                        </tr>
                    </table>
					<?php
				},
				$this->page_slug
			);

			$this->render_fields(
				$this->page_slug,
				'section_wordpress',
				[
					[
						'id'          => self::OPTION_WORDPRESS_NATIVE_TOGGLE,
						'title'       => __( 'Enable Native', "anycomment" ),
						'callback'    => 'input_checkbox',
						'description' => __( 'Allow WordPress native authorization', "anycomment" )
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

                <div class="anycomment-tabs">
                    <aside class="anycomment-tabs__menu anycomment-tabs__menu-socials">
						<?php $this->do_tab_menu( $this->page_slug ) ?>
                    </aside>
                    <div class="anycomment-tabs__container">
						<?php
						$this->do_settings_sections( $this->page_slug, false );
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
		 * {@inheritdoc}
		 */
		protected function do_tab_menu( $page ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}

			echo '<ul>';

			$i = 0;
			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
				$liClasses = ( $i == 0 ? 'current' : '' );

				$liClasses .= ' ' . ( static::isEnabled( str_replace( 'section_', '', $section['id'] ) ) ? 'toggled' : '' );

				$path = sprintf( AnyComment()->plugin_url() . '/assets/img/icons/auth/%s.svg', str_replace( 'section_', 'social-', $section['id'] ) );
				echo '<li class="' . $liClasses . '" data-tab="' . $section['id'] . '">
				<a href="#tab-' . $section['id'] . '"><img src="' . $path . '" />' . $section['title'] . '</a>
				</li>';
				$i ++;
			}
			echo '</ul>';

			?>
            <script>
                jQuery('.anycomment-tabs__menu li').on('click', function () {
                    var data = jQuery(this).attr('data-tab') || '';
                    var tab_id = '#tab-' + data;

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
			<?php
		}

		/**
		 * Custom wrapper over original WordPress core method.
		 *
		 * Part of the Settings API. Use this in a settings page callback function
		 * to output all the sections and fields that were added to that $page with
		 * add_settings_section() and add_settings_field()
		 *
		 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
		 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
		 * @since 0.0.45
		 *
		 * @param string $page The slug name of the page whose settings sections you want to output
		 * @param bool Whether required to have header or not.
		 */
		private function do_settings_sections( $page, $includeHeader = true ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}

			$i = 0;
			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
				if ( $includeHeader && $section['title'] ) {
					echo "<h2>{$section['title']}</h2>\n";
				}

				if ( $includeHeader && $section['callback'] ) {
					call_user_func( $section['callback'], $section );
				}

				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}

				$social     = str_replace( 'section_', '', $section['id'] );
				$guide_link = static::getGuide( [ 'social' => $social ] );


				echo '<div id="tab-' . $section['id'] . '" class="anycomment-tabs__container__tab ' . ( $i === 0 ? 'current' : '' ) . '">';
				echo '<div class="form-table-wrapper ' . ( $guide_link !== null ? 'has-guide' : '' ) . '">';
				echo '<table class="form-table">';
				$this->do_settings_fields( $page, $section['id'] );

				?>
                <tr>
                    <td>
                        <input type="text" id="<?= $social ?>-callback" onclick="this.select()" readonly="readonly"
                               value="<?= AnyCommentSocialAuth::get_callback_url( $social ) ?>">
                        <p class="description"><?= __( 'Callback URL', 'anycomment' ) ?></p>
                    </td>
                </tr>
				<?php
				echo '</table>';
				echo '</div>';

				if ( $guide_link !== null ) {
					$path = sprintf( AnyComment()->plugin_url() . '/assets/img/icons/auth/%s.svg', str_replace( 'section_', 'social-', $section['id'] ) );
					?>
                    <div class="form-table-guide">
                        <div class="anycomment-guide-block">
                            <div class="anycomment-guide-block-social-icon">
                                <img src="<?= $path ?>" alt="">
                            </div>
                            <div class="anycomment-guide-block-header"><?= sprintf( __( "How To Set-Up %s", 'anycomment' ), $section['title'] ) ?></div>
                            <div class="anycomment-guide-block-link">
                                <a target="_blank" href="<?= $guide_link ?>"><?= __( "Read", 'anycomment' ) ?></a>
                            </div>
                        </div>
                    </div>
					<?php
				}

				echo '<div class="clearfix"></div></div>';

				$i ++;
			}
		}

		/**
		 * Custom wrapper over original WordPress core method.
		 *
		 * Part of the Settings API. Use this in a settings page callback function
		 * to output all the sections and fields that were added to that $page with
		 * add_settings_section() and add_settings_field()
		 *
		 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
		 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
		 * @since 0.0.45
		 *
		 * @param string $page The slug name of the page whose settings sections you want to output
		 * @param bool Whether required to have header or not.
		 */
		protected function do_tab_sections( $page, $includeHeader = true ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}

			$i = 0;
			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
				if ( $includeHeader && $section['title'] ) {
					echo "<h2>{$section['title']}</h2>\n";
				}

				if ( $includeHeader && $section['callback'] ) {
					call_user_func( $section['callback'], $section );
				}

				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}

				echo '<div id="tab-' . $section['id'] . '" class="anycomment-tabs__container__tab ' . ( $i === 0 ? 'current' : '' ) . '">';
				echo '<div class="form-table-wrapper">';
				echo '<table class="form-table">';
				do_settings_fields( $page, $section['id'] );
				echo '</table>';
				echo '</div>';

				echo '<div class="clearfix"></div></div>';

				$i ++;
			}
		}

		/**
		 * Custom wrapper over original method.
		 *
		 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
		 *
		 * @since 0.0.45
		 *
		 * @param string $page Slug title of the admin page who's settings fields you want to show.
		 * @param string $section Slug title of the settings section who's fields you want to show.
		 */
		public function do_settings_fields( $page, $section ) {
			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
				$class = '';

				if ( ! empty( $field['args']['class'] ) ) {
					$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
				}

				echo "<tr{$class}>";

				if ( ! empty( $field['args']['label_for'] ) ) {
					echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
				} else {
					echo '<th scope="row">' . $field['title'] . '</th>';
				}

				echo '<td>';
				call_user_func( $field['callback'], $field['args'] );
				echo '</td>';
				echo '</tr>';
			}
		}

		/**
		 * Check whether social enabled by name.
		 *
		 * @param string $name Social network name. Will be lowercased.
		 *
		 * @return mixed|null
		 */
		public static function isEnabled( $name ) {
			return static::instance()->getOption( sprintf( 'social_%s_toggle_field', strtolower( $name ) ) ) !== null;
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
		 * Check whether Yahoo social is on.
		 *
		 * @return bool
		 */
		public static function isYahooOn() {
			return static::instance()->getOption( self::OPTION_YAHOO_TOGGLE ) !== null;
		}

		/**
		 * Get Yahoo App ID.
		 *
		 * @return int|null
		 */
		public static function getYahooAppId() {
			return static::instance()->getOption( self::OPTION_YAHOO_APP_ID );
		}

		/**
		 * Get Yahoo Secure key.
		 *
		 * @return string|null
		 */
		public static function getYahooClientSecret() {
			return static::instance()->getOption( self::OPTION_YAHOO_CLIENT_SECRET );
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
		 * Check whether Instagram social is on.
		 *
		 * @return bool
		 */
		public static function isInstagramOn() {
			return static::instance()->getOption( self::OPTION_INSTAGRAM_TOGGLE ) !== null;
		}

		/**
		 * Get Instagram client ID.
		 *
		 * @return int|null
		 */
		public static function getInstagramClientId() {
			return static::instance()->getOption( self::OPTION_INSTAGRAM_CLIENT_ID );
		}

		/**
		 * Get Instagram secret key.
		 *
		 * @return string|null
		 */
		public static function getInstagramClientSecret() {
			return static::instance()->getOption( self::OPTION_INSTAGRAM_CLIENT_SECRET );
		}

		/**
		 * Check whether Twitch social is on.
		 *
		 * @return bool
		 */
		public static function isTwitchOn() {
			return static::instance()->getOption( self::OPTION_TWITCH_TOGGLE ) !== null;
		}

		/**
		 * Get Twitch client ID.
		 *
		 * @return string|null
		 */
		public static function getTwitchClientId() {
			return static::instance()->getOption( self::OPTION_TWITCH_CLIENT_ID );
		}

		/**
		 * Get Twitch secret key.
		 *
		 * @return string|null
		 */
		public static function getTwitchClientSecret() {
			return static::instance()->getOption( self::OPTION_TWITCH_CLIENT_SECRET );
		}

		/**
		 * Check whether Dribbble social is on.
		 *
		 * @return bool
		 */
		public static function isDribbbleOn() {
			return static::instance()->getOption( self::OPTION_DRIBBBLE_TOGGLE ) !== null;
		}

		/**
		 * Get Dribbble client ID.
		 *
		 * @return string|null
		 */
		public static function getDribbbleClientId() {
			return static::instance()->getOption( self::OPTION_DRIBBBLE_CLIENT_ID );
		}

		/**
		 * Get Dribbble secret key.
		 *
		 * @return string|null
		 */
		public static function getDribbbleClientSecret() {
			return static::instance()->getOption( self::OPTION_DRIBBBLE_CLIENT_SECRET );
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

		/**
		 * Check whether WordPress in-build login is on.
		 *
		 * @return bool
		 */
		public static function isWordPressOn() {
			return static::instance()->getOption( self::OPTION_WORDPRESS_NATIVE_TOGGLE ) !== null;
		}

		/**
		 * Get guide.
		 *
		 * @param null $options Options. See list below.
		 * - native (bool) Takes language from WordPress and trying to get guide for it.
		 * - lang (string) Use this language to search for guide.
		 * - social (string) Social name. Will be lowecased.
		 *
		 * @return null|string NULL when no guide exists for native or specified lang or social, otherwise string.
		 */
		public static function getGuide( $options = null ) {
			$isNative = isset( $options['native'] );
			$lang     = isset( $options['lang'] ) ? trim( $options['lang'] ) : null;
			$social   = isset( $options['social'] ) ? trim( $options['social'] ) : null;

			if ( $social === null ) {
				return null;
			}

			$searchLang = null;

			if ( $isNative || $lang === null ) {
				$searchLang = get_locale();

				if ( strlen( $searchLang ) > 2 ) {
					$searchLang = substr( $searchLang, 0, 2 );
				}
			}

			$guides = static::getGuides();

			// When there are guides for specified language
			if ( ! isset( $guides[ $searchLang ] ) ) {
				return null;
			}

			// When there are guides for language, but no for specific social
			if ( ! isset( $guides[ $searchLang ][ $social ] ) ) {
				return null;
			}

			return $guides[ $searchLang ][ $social ];
		}

		/**
		 * Get list of available guides.
		 *
		 * @return array
		 */
		public static function getGuides() {
			return [
				'ru' => [
					AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => 'https://anycomment.io/ru/api-vkontakte/',
					AnyCommentSocialAuth::SOCIAL_TWITTER       => 'https://anycomment.io/ru/api-twitter/',
					AnyCommentSocialAuth::SOCIAL_FACEBOOK      => 'https://anycomment.io/ru/api-facebook/',
					AnyCommentSocialAuth::SOCIAL_GOOGLE        => 'https://anycomment.io/ru/api-google/',
					AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => 'https://anycomment.io/ru/api-odnoklassniki/',
					AnyCommentSocialAuth::SOCIAL_GITHUB        => 'https://anycomment.io/ru/api-github/',
					AnyCommentSocialAuth::SOCIAL_INSTAGRAM     => 'https://anycomment.io/ru/api-instagram/',
					AnyCommentSocialAuth::SOCIAL_TWITCH        => 'https://anycomment.io/ru/api-twitch/',
				],
				'en' => [
					AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => 'https://anycomment.io/api-vkontakte/',
					AnyCommentSocialAuth::SOCIAL_TWITTER       => 'https://anycomment.io/api-twitter/',
					AnyCommentSocialAuth::SOCIAL_FACEBOOK      => 'https://anycomment.io/api-facebook/',
					AnyCommentSocialAuth::SOCIAL_GOOGLE        => 'https://anycomment.io/api-google/',
					AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => 'https://anycomment.io/ru/api-odnoklassniki/',
					AnyCommentSocialAuth::SOCIAL_GITHUB        => 'https://anycomment.io/api-github/',
					AnyCommentSocialAuth::SOCIAL_INSTAGRAM     => 'https://anycomment.io/api-instagram/',
					AnyCommentSocialAuth::SOCIAL_TWITCH        => 'https://anycomment.io/api-twitch/',
					AnyCommentSocialAuth::SOCIAL_DRIBBBLE      => 'https://anycomment.io/api-dribbble/',
				]
			];
		}
	}
endif;

