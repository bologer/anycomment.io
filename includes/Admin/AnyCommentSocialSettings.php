<?php

namespace AnyComment\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Rest\AnyCommentSocialAuth;

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

		// Vkontakte
		$this->render_fields(
			[
				'id'   => 'section_vkontakte',
				'name' => __( 'VK', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_VK_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => esc_html( __( 'Allow VK authorization', "anycomment" ) ),
				],
				[
					'id'          => self::OPTION_VK_APP_ID,
					'title'       => __( 'Application ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter app id. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment" ), 'https://vk.com/apps?act=manage' )
				],
				[
					'id'          => self::OPTION_VK_SECRET,
					'title'       => __( 'Secure key', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter secure key. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment" ), 'https://vk.com/apps?act=manage' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="vk-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="vk-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_vk_callback() ?>">
                        </div>
						<?php
					}
				],
			]
		);

		// Twitter
		$this->render_fields(
			[
				'id'   => 'section_twitter',
				'name' => __( 'Twitter', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_TWITTER_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Twitter authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_TWITTER_CONSUMER_KEY,
					'title'       => __( 'Consumer Key', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter consumer key. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://apps.twitter.com/' )
				],
				[
					'id'          => self::OPTION_TWITTER_CONSUMER_SECRET,
					'title'       => __( 'Consumer Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter consumer secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://apps.twitter.com/' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="twitter-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="twitter-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_twitter_callback() ?>">
                        </div>
						<?php
					}
				]
			]
		);

		// Facebook
		$this->render_fields(
			[
				'id'   => 'section_facebook',
				'name' => __( 'Facebook', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_FACEBOOK_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Facebook authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_FACEBOOK_APP_ID,
					'title'       => __( 'App ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter app id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://developers.facebook.com/apps/' )
				],
				[
					'id'          => self::OPTION_FACEBOOK_APP_SECRET,
					'title'       => __( 'App Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter app secret. Can be found in the list of <a href="%" target="_blank">apps</a>', "anycomment" ), 'https://developers.facebook.com/apps/' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="facebook-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="facebook-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_facebook_callback() ?>">
                        </div>
						<?php
					}
				]
			]
		);

		// Google
		$this->render_fields(
			[
				'id'   => 'section_google',
				'name' => __( 'Google', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_GOOGLE_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Google authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_GOOGLE_CLIENT_ID,
					'title'       => __( 'Client ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://console.developers.google.com/apis/credentials' )
				],
				[
					'id'          => self::OPTION_GOOGLE_SECRET,
					'title'       => __( 'Client Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://console.developers.google.com/apis/credentials' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="google-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="google-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_google_callback() ?>">
                        </div>
						<?php
					},
				]
			]
		);

		// GutHub
		$this->render_fields(
			[
				'id'   => 'section_github',
				'name' => __( 'GitHub', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_GITHUB_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow GitHub authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_GITHUB_CLIENT_ID,
					'title'       => __( 'Client ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://github.com/settings/developers' )
				],
				[
					'id'          => self::OPTION_GITHUB_SECRET,
					'title'       => __( 'Client Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://github.com/settings/developers' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="github-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="github-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_github_callback() ?>">
                        </div>
						<?php
					},
				]
			]
		);


		// Odnoklassniki
		$this->render_fields(
			[
				'id'   => 'section_odnoklassniki',
				'name' => __( 'Odnoklassniki', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_OK_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Odnoklassniki authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_OK_APP_ID,
					'title'       => __( 'App ID', "anycomment" ),
					'type'        => 'text',
					'description' => __( 'Enter app id. Can be found in the email sent to you by Odnoklassniki', "anycomment" ),
				],
				[
					'id'          => self::OPTION_OK_APP_KEY,
					'title'       => __( 'App Key', "anycomment" ),
					'type'        => 'text',
					'description' => __( 'Enter app key. Can be found in the email sent to you by Odnoklassniki', "anycomment" ),
				],
				[
					'id'          => self::OPTION_OK_APP_SECRET,
					'title'       => __( 'App Secret', "anycomment" ),
					'type'        => 'text',
					'description' => __( 'Enter client secret. Can be found in the email sent to you by Odnoklassniki', "anycomment" ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="ok-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="ok-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_ok_callback() ?>">
                        </div>
						<?php
					}
				]
			]
		);

		// Instagram
		$this->render_fields(
			[
				'id'   => 'section_instagram',
				'name' => __( 'Instagram', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_INSTAGRAM_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Instagram authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_INSTAGRAM_CLIENT_ID,
					'title'       => __( 'Client ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client id. Can be found in <a href="%s" target="_blank">Manage Clients</a>', "anycomment" ), 'https://www.instagram.com/developer/clients/manage/' )
				],
				[
					'id'          => self::OPTION_INSTAGRAM_CLIENT_SECRET,
					'title'       => __( 'Client Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client secret. Can be found in <a href="%s" target="_blank">Manage Clients</a>', "anycomment" ), 'https://www.instagram.com/developer/clients/manage/' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="instagram-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="instagram-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_instagram_callback() ?>">
                        </div>
						<?php
					},

				]
			]
		);


		// Twitch
		$this->render_fields(
			[
				'id'   => 'section_twitch',
				'name' => __( 'Twitch', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_TWITCH_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Twitch authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_TWITCH_CLIENT_ID,
					'title'       => __( 'Client ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client id. It can be found in the <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://glass.twitch.tv/console/apps' )
				],
				[
					'id'          => self::OPTION_TWITCH_CLIENT_SECRET,
					'title'       => __( 'Client Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://glass.twitch.tv/console/apps' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="twitch-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="twitch-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_twitch_callback() ?>">
                        </div>
						<?php
					},

				]
			]
		);


		// Dribbble
		$this->render_fields(
			[
				'id'   => 'section_dribbble',
				'name' => __( 'Dribbble', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_DRIBBBLE_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Dribbble authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_DRIBBBLE_CLIENT_ID,
					'title'       => __( 'Client ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client id. It can be found in the <a href="%s" target="_blank">your applications</a>', "anycomment" ), 'https://dribbble.com/account/applications' )
				],
				[
					'id'          => self::OPTION_DRIBBBLE_CLIENT_SECRET,
					'title'       => __( 'Client Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">your applications</a>', "anycomment" ), 'https://dribbble.com/account/applications' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="dribbble-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="dribbble-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_dribbble_callback() ?>">
                        </div>
						<?php
					},

				]
			]
		);

		// Yahoo
		$this->render_fields(
			[
				'id'   => 'section_yahoo',
				'name' => __( 'Yahoo', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_YAHOO_TOGGLE,
					'title'       => __( 'Enable', "anycomment" ),
					'type'        => 'checkbox',
					'description' => __( 'Allow Yahoo authorization', "anycomment" )
				],
				[
					'id'          => self::OPTION_YAHOO_APP_ID,
					'title'       => __( 'App ID', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter app id. It can be found in the <a href="%s" target="_blank">my apps</a>', "anycomment" ), 'https://developer.yahoo.com/apps/' )
				],
				[
					'id'          => self::OPTION_YAHOO_CLIENT_SECRET,
					'title'       => __( 'Client Secret', "anycomment" ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">my apps</a>', "anycomment" ), 'https://developer.yahoo.com/apps/' ),
					'after'       => function () {
						?>
                        <div class="cell anycomment-form-wrapper__field">
                            <label for="yahoo-callback"><?= __( 'Callback URL', 'anycomment' ) ?></label>
                            <input type="text" id="yahoo-callback" onclick="this.select()" readonly="readonly"
                                   value="<?= AnyCommentSocialAuth::get_yahoo_callback() ?>">
                        </div>
						<?php
					},
				]
			]
		);


		// WordPress
		$this->render_fields(
			[
				'id'   => 'section_wordpress',
				'name' => __( 'WordPress', "anycomment" ),
			],
			[
				[
					'id'          => self::OPTION_WORDPRESS_NATIVE_TOGGLE,
					'title'       => __( 'Enable Native', "anycomment" ),
					'type'        => 'checkbox',
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
        <form action="options.php" method="post" class="anycomment-form" novalidate>
			<?php settings_fields( $this->option_group ) ?>

            <div class="anycomment-tabs grid-x grid-margin-x">
                <aside class="cell large-5 medium-5 small-12 anycomment-tabs__menu anycomment-tabs__menu-socials">
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

			$liClasses .= ' ' . ( static::is_enabled( str_replace( 'section_', '', $section['id'] ) ) ? 'toggled' : '' );

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
	protected function do_tab_sections( $page, $includeHeader = true ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		$i = 0;
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if ( $includeHeader && isset( $section['title'] ) ) {
				echo "<h2>{$section['title']}</h2>";
			}

			if ( $includeHeader && $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}

			echo '<div id="tab-' . $section['id'] . '" class="anycomment-tabs__container__tab ' . ( $i === 0 ? 'current' : '' ) . '">';
			echo '<div class="grid-x anycomment-form-wrapper">';
			$this->do_settings_fields( $page, $section['id'] );

			$social     = str_replace( 'section_', '', $section['id'] );
			$guide_link = static::getGuide( [ 'social' => $social ] );

			if ( $guide_link !== null ) {
				$path = sprintf( AnyComment()->plugin_url() . '/assets/img/icons/auth/%s.svg', str_replace( 'section_', 'social-', $section['id'] ) );
				?>
                <div class="cell anycomment-form-wrapper__field">
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
			echo '</div>';

			echo '</div>';

			$i ++;
		}
	}

	/**
	 * Check whether social enabled by name.
	 *
	 * @param string $name Social network name. Will be lowercased.
	 *
	 * @return mixed|null
	 */
	public static function is_enabled( $name ) {
		return static::instance()->get_option( sprintf( 'social_%s_toggle_field', strtolower( $name ) ) ) !== null;
	}

	/**
	 * Check if user enter at least some information about socials.
	 *
	 * @return bool
	 */
	public static function has_any_social() {
		return static::instance()->has_options();
	}

	/**
	 * Check whether VK social is on.
	 *
	 * @return bool
	 */
	public static function is_vk_active() {
		return static::instance()->get_option( self::OPTION_VK_TOGGLE ) !== null;
	}

	/**
	 * Get VK App ID.
	 *
	 * @return int|null
	 */
	public static function get_vk_app_id() {
		return static::instance()->get_option( self::OPTION_VK_APP_ID );
	}

	/**
	 * Get VK Secure key.
	 *
	 * @return string|null
	 */
	public static function get_vk_secure_key() {
		return static::instance()->get_option( self::OPTION_VK_SECRET );
	}

	/**
	 * Check whether Yahoo social is on.
	 *
	 * @return bool
	 */
	public static function is_yahoo_active() {
		return static::instance()->get_option( self::OPTION_YAHOO_TOGGLE ) !== null;
	}

	/**
	 * Get Yahoo App ID.
	 *
	 * @return int|null
	 */
	public static function get_ahoo_app_id() {
		return static::instance()->get_option( self::OPTION_YAHOO_APP_ID );
	}

	/**
	 * Get Yahoo Secure key.
	 *
	 * @return string|null
	 */
	public static function get_yahoo_client_secret() {
		return static::instance()->get_option( self::OPTION_YAHOO_CLIENT_SECRET );
	}

	/**
	 * Check whether GitHub social is on.
	 *
	 * @return bool
	 */
	public static function is_github_active() {
		return static::instance()->get_option( self::OPTION_GITHUB_TOGGLE ) !== null;
	}

	/**
	 * Get GitHub client ID.
	 *
	 * @return int|null
	 */
	public static function get_github_client_id() {
		return static::instance()->get_option( self::OPTION_GITHUB_CLIENT_ID );
	}

	/**
	 * Get GitHub secret key.
	 *
	 * @return string|null
	 */
	public static function get_github_secret_key() {
		return static::instance()->get_option( self::OPTION_GITHUB_SECRET );
	}

	/**
	 * Check whether Instagram social is on.
	 *
	 * @return bool
	 */
	public static function is_instagram_active() {
		return static::instance()->get_option( self::OPTION_INSTAGRAM_TOGGLE ) !== null;
	}

	/**
	 * Get Instagram client ID.
	 *
	 * @return int|null
	 */
	public static function get_instagram_client_id() {
		return static::instance()->get_option( self::OPTION_INSTAGRAM_CLIENT_ID );
	}

	/**
	 * Get Instagram secret key.
	 *
	 * @return string|null
	 */
	public static function get_nstagram_client_secret() {
		return static::instance()->get_option( self::OPTION_INSTAGRAM_CLIENT_SECRET );
	}

	/**
	 * Check whether Twitch social is on.
	 *
	 * @return bool
	 */
	public static function is_twitch_active() {
		return static::instance()->get_option( self::OPTION_TWITCH_TOGGLE ) !== null;
	}

	/**
	 * Get Twitch client ID.
	 *
	 * @return string|null
	 */
	public static function get_twitch_client_id() {
		return static::instance()->get_option( self::OPTION_TWITCH_CLIENT_ID );
	}

	/**
	 * Get Twitch secret key.
	 *
	 * @return string|null
	 */
	public static function get_twitch_client_secret() {
		return static::instance()->get_option( self::OPTION_TWITCH_CLIENT_SECRET );
	}

	/**
	 * Check whether Dribbble social is on.
	 *
	 * @return bool
	 */
	public static function is_dribbble_active() {
		return static::instance()->get_option( self::OPTION_DRIBBBLE_TOGGLE ) !== null;
	}

	/**
	 * Get Dribbble client ID.
	 *
	 * @return string|null
	 */
	public static function get_dribbble_client_id() {
		return static::instance()->get_option( self::OPTION_DRIBBBLE_CLIENT_ID );
	}

	/**
	 * Get Dribbble secret key.
	 *
	 * @return string|null
	 */
	public static function get_dribbble_client_secret() {
		return static::instance()->get_option( self::OPTION_DRIBBBLE_CLIENT_SECRET );
	}

	/**
	 * Check whether Twitter is on.
	 *
	 * @return bool
	 */
	public static function is_twitter_active() {
		return static::instance()->get_option( self::OPTION_TWITTER_TOGGLE ) !== null;
	}

	/**
	 * Get Twitter consumer key.
	 *
	 * @return string|null
	 */
	public static function get_twitter_consumer_key() {
		return static::instance()->get_option( self::OPTION_TWITTER_CONSUMER_KEY );
	}

	/**
	 * Get Twitter consumer secret.
	 *
	 * @return string|null
	 */
	public static function get_twitter_consumer_secret() {
		return static::instance()->get_option( self::OPTION_TWITTER_CONSUMER_SECRET );
	}

	/**
	 * Check whether Facebook social is on.
	 *
	 * @return bool
	 */
	public static function is_facebook_active() {
		return static::instance()->get_option( self::OPTION_FACEBOOK_TOGGLE ) !== null;
	}

	/**
	 * Get Facebook App ID.
	 *
	 * @return int|null
	 */
	public static function get_facebook_app_id() {
		return static::instance()->get_option( self::OPTION_FACEBOOK_APP_ID );
	}

	/**
	 * Get Facebook Secure key.
	 *
	 * @return string|null
	 */
	public static function get_facebook_app_secret() {
		return static::instance()->get_option( self::OPTION_FACEBOOK_APP_SECRET );
	}

	/**
	 * Check whether Google social is on.
	 *
	 * @return bool
	 */
	public static function is_google_active() {
		return static::instance()->get_option( self::OPTION_GOOGLE_TOGGLE ) !== null;
	}

	/**
	 * Get Google Client ID.
	 *
	 * @return int|null
	 */
	public static function get_google_client_id() {
		return static::instance()->get_option( self::OPTION_GOOGLE_CLIENT_ID );
	}

	/**
	 * Get Google secret key.
	 *
	 * @return string|null
	 */
	public static function get_google_secret() {
		return static::instance()->get_option( self::OPTION_GOOGLE_SECRET );
	}

	/**
	 * Check whether Odnoklassniki social is on.
	 *
	 * @return bool
	 */
	public static function is_odnoklassniki_on() {
		return static::instance()->get_option( self::OPTION_OK_TOGGLE ) !== null;
	}

	/**
	 * Get Odnoklassniki app ID.
	 *
	 * @return int|null
	 */
	public static function get_odnoklassniki_app_id() {
		return static::instance()->get_option( self::OPTION_OK_APP_ID );
	}

	/**
	 * Get Odnoklassniki app key.
	 *
	 * @return int|null
	 */
	public static function get_odnoklassniki_app_key() {
		return static::instance()->get_option( self::OPTION_OK_APP_KEY );
	}

	/**
	 * Get Odnoklassniki app secret key.
	 *
	 * @return string|null
	 */
	public static function get_odnoklassniki_app_secret() {
		return static::instance()->get_option( self::OPTION_OK_APP_SECRET );
	}

	/**
	 * Check whether WordPress in-build login is on.
	 *
	 * @return bool
	 */
	public static function is_wordpress_native_active() {
		return static::instance()->get_option( self::OPTION_WORDPRESS_NATIVE_TOGGLE ) !== null;
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
