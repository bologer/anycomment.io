<?php

namespace AnyComment\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\AnyCommentServiceApi;
use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Helpers\AnyCommentTemplate;
use AnyComment\Options\AnyCommentOptionManager;
use AnyComment\Rest\AnyCommentSocialAuth;

/**
 * AnyCommentAdminPages helps to process website authentication.
 */
class AnyCommentSocialSettings extends AnyCommentOptionManager {
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
	 * @inheritdoc
	 */
	protected $field_options = [
		'wrapper' => '<div class="cell anycomment-form-wrapper__field">{content}</div>'
	];

	/**
	 * @inheritdoc
	 */
	protected $section_options = [
		'wrapper' => '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab" id="{id}">{content}</div>'
	];

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
	 * Steam
	 */
	const OPTION_STEAM_TOGGLE = 'social_steam_toggle_field';
	const OPTION_STEAM_SECRET = 'social_steam_client_secret_field';

	/**
	 * Yandex
	 */
	const OPTION_YANDEX_TOGGLE = 'social_yandex_toggle_field';
	const OPTION_YANDEX_CLIENT_ID = 'social_yandex_client_id_field';
	const OPTION_YANDEX_CLIENT_SECRET = 'social_yandex_client_secret_field';

	/**
	 * Mailru
	 */
	const OPTION_MAILRU_TOGGLE = 'social_mailru_toggle_field';
	const OPTION_MAILRU_CLIENT_ID = 'social_mailru_client_id_field';
	const OPTION_MAILRU_CLIENT_SECRET = 'social_mailru_client_secret_field';

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

		$this->form()
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'vkontakte' )
			          ->set_title( __( 'Vkontakte', 'anycomment' ) )
			          ->set_wrapper( '<div class="grid-x anycomment-form-wrapper anycomment-tabs__container__tab current" id="{id}">{content}</div>' )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_VK_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( esc_html( __( 'Allow VK authorization', "anycomment" ) ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_VK_APP_ID )
				               ->set_title( __( 'Application ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter app id. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment" ), 'https://vk.com/apps?act=manage' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_VK_SECRET )
				               ->set_title( __( 'Secure key', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter secure key. Can be found in <a href="%s" target="_blank">apps</a> page', "anycomment" ), 'https://vk.com/apps?act=manage' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_vk_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="vkontakte-callback">' . $callback_label . '</label>
									    <input type="text" id="vkontakte-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'twitter' )
			          ->set_title( __( 'Twitter', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_TWITTER_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Twitter authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_TWITTER_CONSUMER_KEY )
				               ->set_title( __( 'Consumer Key', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter consumer key. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://apps.twitter.com/' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_TWITTER_CONSUMER_SECRET )
				               ->set_title( __( 'Consumer Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter consumer secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://apps.twitter.com/' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_twitter_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="twitter-callback">' . $callback_label . '</label>
									    <input type="text" id="twitter-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } )
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'facebook' )
			          ->set_title( __( 'Facebook', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_FACEBOOK_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Facebook authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_FACEBOOK_APP_ID )
				               ->set_title( __( 'App ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter app id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://developers.facebook.com/apps/' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_FACEBOOK_APP_SECRET )
				               ->set_title( __( 'App Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter app secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://developers.facebook.com/apps/' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_facebook_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="facebook-callback">' . $callback_label . '</label>
									    <input type="text" id="facebook-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'google' )
			          ->set_title( __( 'Google', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_GOOGLE_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Google authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_GOOGLE_CLIENT_ID )
				               ->set_title( __( 'Client ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://console.developers.google.com/apis/credentials' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_GOOGLE_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://console.developers.google.com/apis/credentials' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_google_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="google-callback">' . $callback_label . '</label>
									    <input type="text" id="google-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'github' )
			          ->set_title( __( 'GitHub', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_GITHUB_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow GitHub authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_GITHUB_CLIENT_ID )
				               ->set_title( __( 'Client ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client id. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://github.com/settings/developers' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_GITHUB_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client secret. Can be found in the list of <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://github.com/settings/developers' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_github_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="github-callback">' . $callback_label . '</label>
									    <input type="text" id="github-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'odnoklassniki' )
			          ->set_title( __( 'Odnoklassniki', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_OK_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Odnoklassniki authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_OK_APP_ID )
				               ->set_title( __( 'App ID', "anycomment" ) )
				               ->set_description( __( 'Enter app id. Can be found in the email sent to you by Odnoklassniki', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_OK_APP_KEY )
				               ->set_title( __( 'App Key', "anycomment" ) )
				               ->set_description( __( 'Enter app key. Can be found in the email sent to you by Odnoklassniki', "anycomment" ) ),


				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_OK_APP_SECRET )
				               ->set_title( __( 'App Secret', "anycomment" ) )
				               ->set_description( __( 'Enter client secret. Can be found in the email sent to you by Odnoklassniki', "anycomment" ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_ok_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="ok-callback">' . $callback_label . '</label>
									    <input type="text" id="ok-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'twitch' )
			          ->set_title( __( 'Twitch', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_TWITCH_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Twitch authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_TWITCH_CLIENT_ID )
				               ->set_title( __( 'Client ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client id. It can be found in the <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://glass.twitch.tv/console/apps' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_TWITCH_CLIENT_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">apps</a>', "anycomment" ), 'https://glass.twitch.tv/console/apps' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_twitch_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="twitch-callback">' . $callback_label . '</label>
									    <input type="text" id="twitch-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'steam' )
			          ->set_title( __( 'Steam', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_STEAM_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Steam authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_STEAM_SECRET )
				               ->set_title( __( 'Key', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter key. It can be found in the <a href="%s" target="_blank">Register Steam Web API Key</a>', "anycomment" ), 'https://steamcommunity.com/dev/registerkey' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_steam_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="steam-callback">' . $callback_label . '</label>
									    <input type="text" id="steam-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'yandex' )
			          ->set_title( __( 'Yandex', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_YANDEX_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Yandex authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_YANDEX_CLIENT_ID )
				               ->set_title( __( 'ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter id. It can be found after you <a href="%s" target="_blank">register</a> your web-application', "anycomment" ), 'https://oauth.yandex.ru/client/new' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_YANDEX_CLIENT_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter password (NOT your email password). It can be found after you <a href="%s" target="_blank">register</a> your web-application', "anycomment" ), 'https://oauth.yandex.ru/client/new' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_yandex_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="yandex-callback">' . $callback_label . '</label>
									    <input type="text" id="yandex-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'mailru' )
			          ->set_title( __( 'Mail.Ru', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_MAILRU_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Mail.Ru authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_MAILRU_CLIENT_ID )
				               ->set_title( __( 'ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter id. It can be found after you <a href="%s" target="_blank">register</a> your web-application', "anycomment" ), 'http://api.mail.ru/sites/my/add' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_MAILRU_CLIENT_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client secret. It can be found after you <a href="%s" target="_blank">register</a> your web-application', "anycomment" ), 'https://api.mail.ru/sites/my/add' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_mailru_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="mailru-callback">' . $callback_label . '</label>
									    <input type="text" id="mailru-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'dribbble' )
			          ->set_title( __( 'Dribbble', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_DRIBBBLE_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Dribbble authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_DRIBBBLE_CLIENT_ID )
				               ->set_title( __( 'Client ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client id. It can be found in the <a href="%s" target="_blank">your applications</a>', "anycomment" ), 'https://dribbble.com/account/applications' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_DRIBBBLE_CLIENT_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">your applications</a>', "anycomment" ), 'https://dribbble.com/account/applications' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_dribbble_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="dribbble-callback">' . $callback_label . '</label>
									    <input type="text" id="dribbble-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'yahoo' )
			          ->set_title( __( 'Yahoo', "anycomment" ) )
			          ->set_visible( false )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_YAHOO_TOGGLE )
				               ->set_title( __( 'Enable', "anycomment" ) )
				               ->set_description( __( 'Allow Yahoo authorization', "anycomment" ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_YAHOO_APP_ID )
				               ->set_title( __( 'App ID', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter app id. It can be found in the <a href="%s" target="_blank">my apps</a>', "anycomment" ), 'https://developer.yahoo.com/apps/' ) ),

				          $this->field_builder()
				               ->text()
				               ->set_id( self::OPTION_YAHOO_CLIENT_SECRET )
				               ->set_title( __( 'Client Secret', "anycomment" ) )
				               ->set_description( sprintf( __( 'Enter client secret. It can be found in the <a href="%s" target="_blank">my apps</a>', "anycomment" ), 'https://developer.yahoo.com/apps/' ) )
				               ->set_after( function () {
					               $callback_label = __( 'Callback URL', 'anycomment' );
					               $callback_url   = AnyCommentSocialAuth::get_yahoo_callback();

					               return '<div class="cell anycomment-form-wrapper__field">
									    <label for="yahoo-callback">' . $callback_label . '</label>
									    <input type="text" id="yahoo-callback" onclick="this.select()" readonly="readonly"
									           value="' . $callback_url . '">
									</div>';
				               } ),
			          ] )
		     )
		     ->add_section(
			     $this->section_builder()
			          ->set_id( 'wordpress' )
			          ->set_title( __( 'WordPress', "anycomment" ) )
			          ->set_fields( [
				          $this->field_builder()
				               ->checkbox()
				               ->set_id( self::OPTION_WORDPRESS_NATIVE_TOGGLE )
				               ->set_title( __( 'Enable Native', "anycomment" ) )
				               ->set_description( __( 'Allow WordPress native authorization', "anycomment" ) ),
			          ] )
		     );
	}

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		if ( AnyCommentServiceApi::is_ready() && AnyCommentIntegrationSettings::is_sass_comments_show() ) {
			return AnyCommentTemplate::render( 'admin/saas-notice' );
		}

		$sections_html = '<div class="anycomment-notice anycomment-success">';
		$sections_html .= sprintf(
			__( 'Try out our <a href="%s" target="_blank">AnyComment Cloud</a>. We already configured all of the socials for you, just choose what you need.', 'anycomment' ),
			'https://anycomment.io/' . AnyCommentLinkHelper::get_service_article_language() . '/docs/connect-to-saas'
		);
		$sections_html .= '</div>';
		$sections_html .= '<form action="' . esc_url( admin_url( "admin-post.php" ) ) . '" id="' . $this->get_page_slug() . '" method="post" class="anycomment-form" novalidate>';

		$redirect_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url( $_SERVER['REQUEST_URI'] ) : '';

		$sections_html .= '<input type="hidden" name="redirect" value="' . $redirect_url . '">';
		$sections_html .= '<input type="hidden" name="action" value="' . $this->option_name . '">';
		$sections_html .= '<input type="hidden" name="nonce" value="' . wp_create_nonce( $this->option_name ) . '" />';

		$options = $this->options;

		foreach ( $options as $option ) {
			$sections = $option->get_sections();

			if ( ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					$section->set_callback( function () use ( $section ) {

						$alt        = $section->get_title();
						$guide_link = static::get_guide( [ 'social' => $section->get_id() ] );
						$img_src    = sprintf( AnyComment()->plugin_url() . '/assets/img/socials/%s.svg', $section->get_id() );
						$title      = sprintf( __( "How To Set-Up %s", 'anycomment' ), $section->get_title() );
						$read_more  = __( "Read", 'anycomment' );

						return <<<EOT
<div class="anycomment-guide-block">
    <div class="anycomment-guide-block-social-icon">
        <img src="$img_src" alt="$alt">
    </div>
    <div class="anycomment-guide-block-header">$title</div>
    <div class="anycomment-guide-block-link">
        <a target="_blank" href="$guide_link">$read_more</a>
    </div>
</div>
EOT;
					} );

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
	<aside class="cell large-5 medium-5 small-12 anycomment-tabs__menu anycomment-tabs__menu-socials">
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

				$liClasses .= ' ' . ( static::is_enabled( str_replace( 'section_', '', $section_id ) ) ? 'toggled' : '' );

				$path = sprintf( AnyComment()->plugin_url() . '/assets/img/socials/%s.svg', $section_id );

				$html .= '<li class="' . $liClasses . '" style="display: ' . esc_attr( $section->is_visible() ? 'block' : 'none' ) . '" data-tab="' . $section_id . '">
				<a href="#tab-' . $section_id . '"><img src="' . $path . '" />' . $section->get_title() . '</a>
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
	 * Check whether social enabled by name.
	 *
	 * @param string $name Social network name. Will be lowercased.
	 *
	 * @return mixed|null
	 */
	public static function is_enabled( $name ) {
		return static::instance()->get_db_option( sprintf( 'social_%s_toggle_field', strtolower( $name ) ) ) !== null;
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
		return static::instance()->get_db_option( self::OPTION_VK_TOGGLE ) !== null;
	}

	/**
	 * Get VK App ID.
	 *
	 * @return int|null
	 */
	public static function get_vk_app_id() {
		return static::instance()->get_db_option( self::OPTION_VK_APP_ID );
	}

	/**
	 * Get VK Secure key.
	 *
	 * @return string|null
	 */
	public static function get_vk_secure_key() {
		return static::instance()->get_db_option( self::OPTION_VK_SECRET );
	}

	/**
	 * Check whether Mail.Ru is on.
	 *
	 * @return bool
	 */
	public static function is_mailru_active() {
		return static::instance()->get_db_option( self::OPTION_MAILRU_TOGGLE ) !== null;
	}

	/**
	 * Get Mail.Ru client id.
	 *
	 * @return string|null
	 */
	public static function get_mailru_client_id() {
		return static::instance()->get_db_option( self::OPTION_MAILRU_CLIENT_ID );
	}

	/**
	 * Get Mail.Ru client secret.
	 *
	 * @return string|null
	 */
	public static function get_mailru_client_secret() {
		return static::instance()->get_db_option( self::OPTION_MAILRU_CLIENT_SECRET );
	}

	/**
	 * Check whether Yandex is on.
	 *
	 * @return bool
	 */
	public static function is_yandex_active() {
		return static::instance()->get_db_option( self::OPTION_YANDEX_TOGGLE ) !== null;
	}

	/**
	 * Get Yandex client id.
	 *
	 * @return string|null
	 */
	public static function get_yandex_client_id() {
		return static::instance()->get_db_option( self::OPTION_YANDEX_CLIENT_ID );
	}

	/**
	 * Get Yandex client secret.
	 *
	 * @return string|null
	 */
	public static function get_yandex_client_secret() {
		return static::instance()->get_db_option( self::OPTION_YANDEX_CLIENT_SECRET );
	}

	/**
	 * Check whether Yahoo social is on.
	 *
	 * @return bool
	 */
	public static function is_yahoo_active() {
		return static::instance()->get_db_option( self::OPTION_YAHOO_TOGGLE ) !== null;
	}

	/**
	 * Get Yahoo App ID.
	 *
	 * @return int|null
	 */
	public static function get_yahoo_app_id() {
		return static::instance()->get_db_option( self::OPTION_YAHOO_APP_ID );
	}

	/**
	 * Get Yahoo Secure key.
	 *
	 * @return string|null
	 */
	public static function get_yahoo_client_secret() {
		return static::instance()->get_db_option( self::OPTION_YAHOO_CLIENT_SECRET );
	}

	/**
	 * Check whether GitHub social is on.
	 *
	 * @return bool
	 */
	public static function is_github_active() {
		return static::instance()->get_db_option( self::OPTION_GITHUB_TOGGLE ) !== null;
	}

	/**
	 * Get GitHub client ID.
	 *
	 * @return int|null
	 */
	public static function get_github_client_id() {
		return static::instance()->get_db_option( self::OPTION_GITHUB_CLIENT_ID );
	}

	/**
	 * Get GitHub secret key.
	 *
	 * @return string|null
	 */
	public static function get_github_secret_key() {
		return static::instance()->get_db_option( self::OPTION_GITHUB_SECRET );
	}

	/**
	 * Check whether Twitch social is on.
	 *
	 * @return bool
	 */
	public static function is_twitch_active() {
		return static::instance()->get_db_option( self::OPTION_TWITCH_TOGGLE ) !== null;
	}

	/**
	 * Get Twitch client ID.
	 *
	 * @return string|null
	 */
	public static function get_twitch_client_id() {
		return static::instance()->get_db_option( self::OPTION_TWITCH_CLIENT_ID );
	}

	/**
	 * Get Twitch secret key.
	 *
	 * @return string|null
	 */
	public static function get_twitch_client_secret() {
		return static::instance()->get_db_option( self::OPTION_TWITCH_CLIENT_SECRET );
	}

	/**
	 * Check whether Dribbble social is on.
	 *
	 * @return bool
	 */
	public static function is_dribbble_active() {
		return static::instance()->get_db_option( self::OPTION_DRIBBBLE_TOGGLE ) !== null;
	}

	/**
	 * Get Dribbble client ID.
	 *
	 * @return string|null
	 */
	public static function get_dribbble_client_id() {
		return static::instance()->get_db_option( self::OPTION_DRIBBBLE_CLIENT_ID );
	}

	/**
	 * Get Dribbble secret key.
	 *
	 * @return string|null
	 */
	public static function get_dribbble_client_secret() {
		return static::instance()->get_db_option( self::OPTION_DRIBBBLE_CLIENT_SECRET );
	}

	/**
	 * Check whether Steam social is on.
	 *
	 * @return bool
	 */
	public static function is_steam_active() {
		return static::instance()->get_db_option( self::OPTION_STEAM_TOGGLE ) !== null;
	}

	/**
	 * Get Steam key.
	 *
	 * @return string|null
	 */
	public static function get_steam_secret() {
		return static::instance()->get_db_option( self::OPTION_STEAM_SECRET );
	}

	/**
	 * Check whether Twitter is on.
	 *
	 * @return bool
	 */
	public static function is_twitter_active() {
		return static::instance()->get_db_option( self::OPTION_TWITTER_TOGGLE ) !== null;
	}

	/**
	 * Get Twitter consumer key.
	 *
	 * @return string|null
	 */
	public static function get_twitter_consumer_key() {
		return static::instance()->get_db_option( self::OPTION_TWITTER_CONSUMER_KEY );
	}

	/**
	 * Get Twitter consumer secret.
	 *
	 * @return string|null
	 */
	public static function get_twitter_consumer_secret() {
		return static::instance()->get_db_option( self::OPTION_TWITTER_CONSUMER_SECRET );
	}

	/**
	 * Check whether Facebook social is on.
	 *
	 * @return bool
	 */
	public static function is_facebook_active() {
		return static::instance()->get_db_option( self::OPTION_FACEBOOK_TOGGLE ) !== null;
	}

	/**
	 * Get Facebook App ID.
	 *
	 * @return int|null
	 */
	public static function get_facebook_app_id() {
		return static::instance()->get_db_option( self::OPTION_FACEBOOK_APP_ID );
	}

	/**
	 * Get Facebook Secure key.
	 *
	 * @return string|null
	 */
	public static function get_facebook_app_secret() {
		return static::instance()->get_db_option( self::OPTION_FACEBOOK_APP_SECRET );
	}

	/**
	 * Check whether Google social is on.
	 *
	 * @return bool
	 */
	public static function is_google_active() {
		return static::instance()->get_db_option( self::OPTION_GOOGLE_TOGGLE ) !== null;
	}

	/**
	 * Get Google Client ID.
	 *
	 * @return int|null
	 */
	public static function get_google_client_id() {
		return static::instance()->get_db_option( self::OPTION_GOOGLE_CLIENT_ID );
	}

	/**
	 * Get Google secret key.
	 *
	 * @return string|null
	 */
	public static function get_google_secret() {
		return static::instance()->get_db_option( self::OPTION_GOOGLE_SECRET );
	}

	/**
	 * Check whether Odnoklassniki social is on.
	 *
	 * @return bool
	 */
	public static function is_odnoklassniki_on() {
		return static::instance()->get_db_option( self::OPTION_OK_TOGGLE ) !== null;
	}

	/**
	 * Get Odnoklassniki app ID.
	 *
	 * @return int|null
	 */
	public static function get_odnoklassniki_app_id() {
		return static::instance()->get_db_option( self::OPTION_OK_APP_ID );
	}

	/**
	 * Get Odnoklassniki app key.
	 *
	 * @return int|null
	 */
	public static function get_odnoklassniki_app_key() {
		return static::instance()->get_db_option( self::OPTION_OK_APP_KEY );
	}

	/**
	 * Get Odnoklassniki app secret key.
	 *
	 * @return string|null
	 */
	public static function get_odnoklassniki_app_secret() {
		return static::instance()->get_db_option( self::OPTION_OK_APP_SECRET );
	}

	/**
	 * Check whether WordPress in-build login is on.
	 *
	 * @return bool
	 */
	public static function is_wordpress_native_active() {
		return static::instance()->get_db_option( self::OPTION_WORDPRESS_NATIVE_TOGGLE ) !== null;
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
	public static function get_guide( $options = null ) {
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

		$guides = static::get_guides();

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
	public static function get_guides() {
		return [
			'ru' => [
				AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => 'https://plugin.anycomment.io/api-vkontakte/',
				AnyCommentSocialAuth::SOCIAL_TWITTER       => 'https://plugin.anycomment.io/api-twitter/',
				AnyCommentSocialAuth::SOCIAL_FACEBOOK      => 'https://plugin.anycomment.io/api-facebook/',
				AnyCommentSocialAuth::SOCIAL_GOOGLE        => 'https://plugin.anycomment.io/api-google/',
				AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => 'https://plugin.anycomment.io/api-odnoklassniki/',
				AnyCommentSocialAuth::SOCIAL_GITHUB        => 'https://plugin.anycomment.io/api-github/',
				AnyCommentSocialAuth::SOCIAL_TWITCH        => 'https://plugin.anycomment.io/api-twitch/',
				AnyCommentSocialAuth::SOCIAL_STEAM         => 'https://plugin.anycomment.io/api-steam/',
				AnyCommentSocialAuth::SOCIAL_YANDEX        => 'https://plugin.anycomment.io/api-yandex/',
				AnyCommentSocialAuth::SOCIAL_MAILRU        => 'https://plugin.anycomment.io/api-mailru/',
			],
			'en' => [
				AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => 'https://plugin.anycomment.io/en/api-vkontakte/',
				AnyCommentSocialAuth::SOCIAL_TWITTER       => 'https://plugin.anycomment.io/en/api-twitter/',
				AnyCommentSocialAuth::SOCIAL_FACEBOOK      => 'https://plugin.anycomment.io/en/api-facebook/',
				AnyCommentSocialAuth::SOCIAL_GOOGLE        => 'https://plugin.anycomment.io/en/api-google/',
				AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => 'https://plugin.anycomment.io/ru/api-odnoklassniki/',
				AnyCommentSocialAuth::SOCIAL_GITHUB        => 'https://plugin.anycomment.io/en/api-github/',
				AnyCommentSocialAuth::SOCIAL_TWITCH        => 'https://plugin.anycomment.io/en/api-twitch/',
				AnyCommentSocialAuth::SOCIAL_DRIBBBLE      => 'https://plugin.anycomment.io/en/api-dribbble/',
				AnyCommentSocialAuth::SOCIAL_STEAM         => 'https://plugin.anycomment.io/en/api-steam/',
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function save_option_keys() {
		return [
			self::OPTION_VK_TOGGLE,
			self::OPTION_VK_APP_ID,
			self::OPTION_VK_SECRET,

			self::OPTION_TWITTER_TOGGLE,
			self::OPTION_TWITTER_CONSUMER_KEY,
			self::OPTION_TWITTER_CONSUMER_SECRET,

			self::OPTION_FACEBOOK_TOGGLE,
			self::OPTION_FACEBOOK_APP_ID,
			self::OPTION_FACEBOOK_APP_SECRET,

			self::OPTION_GOOGLE_TOGGLE,
			self::OPTION_GOOGLE_CLIENT_ID,
			self::OPTION_GOOGLE_SECRET,

			self::OPTION_GITHUB_TOGGLE,
			self::OPTION_GITHUB_CLIENT_ID,
			self::OPTION_GITHUB_SECRET,

			self::OPTION_OK_TOGGLE,
			self::OPTION_OK_APP_ID,
			self::OPTION_OK_APP_KEY,
			self::OPTION_OK_APP_SECRET,

			self::OPTION_TWITCH_TOGGLE,
			self::OPTION_TWITCH_CLIENT_ID,
			self::OPTION_TWITCH_CLIENT_SECRET,

			self::OPTION_STEAM_TOGGLE,
			self::OPTION_STEAM_SECRET,

			self::OPTION_YANDEX_TOGGLE,
			self::OPTION_YANDEX_CLIENT_ID,
			self::OPTION_YANDEX_CLIENT_SECRET,

			self::OPTION_MAILRU_TOGGLE,
			self::OPTION_MAILRU_CLIENT_ID,
			self::OPTION_MAILRU_CLIENT_SECRET,

			self::OPTION_DRIBBBLE_TOGGLE,
			self::OPTION_DRIBBBLE_CLIENT_ID,
			self::OPTION_DRIBBBLE_CLIENT_SECRET,

			self::OPTION_YAHOO_TOGGLE,
			self::OPTION_YAHOO_APP_ID,
			self::OPTION_YAHOO_CLIENT_SECRET,

			self::OPTION_WORDPRESS_NATIVE_TOGGLE,
		];
	}
}
