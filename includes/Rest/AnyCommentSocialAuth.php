<?php

namespace AnyComment\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\AnyCommentCore;
use WP_REST_Request;
use WP_Error;
use WP_User;
use WP_Comment;

use AnyComment\Cache\UserCache;
use AnyComment\AnyCommentUser;
use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Admin\AnyCommentSocialSettings;
use AnyComment\Admin\AnyCommentIntegrationSettings;

use AnyComment\AnyCommentUserMeta;
use AnyComment\AnyCommentAvatars;
use AnyComment\AnyCommentUploadHandler;

use Hybridauth\User\Profile;

/**
 * AC_SocialAuth helps to process website authentication.
 */
class AnyCommentSocialAuth {
	/**
	 * Different social types.
	 * Used for routing, images, etc.
	 */
	const SOCIAL_VKONTAKTE = 'vkontakte';
	const SOCIAL_FACEBOOK = 'facebook';
	const SOCIAL_TWITTER = 'twitter';
	const SOCIAL_GOOGLE = 'google';
	const SOCIAL_GITHUB = 'github';
	const SOCIAL_ODNOKLASSNIKI = 'odnoklassniki';
	const SOCIAL_TWITCH = 'twitch';
	const SOCIAL_DRIBBBLE = 'dribbble';
	const SOCIAL_STEAM = 'steam';
	const SOCIAL_YANDEX = 'yandex';
	const SOCIAL_MAILRU = 'mailru';
	const SOCIAL_YAHOO = 'yahoo';
	const SOCIAL_WORDPRESS = 'wordpress';


	// Cookie name for post URL redirect
	const COOKIE_REDIRECT = 'post_redirect';

	/**
	 * @var \Hybridauth\Hybridauth
	 */
	private $_auth;

	/**
	 * Determine type of social.
	 */
	const META_SOCIAL_TYPE = 'anycomment_social';

	/**
	 * Determine whether user has parent or not.
	 */
	const META_PARENT = 'anycomment_parent';

	/**
	 * Link to social.
	 */
	const META_SOCIAL_LINK = 'anycomment_social_link';

	/**
	 * Original username from social.
	 */
	const META_SOCIAL_ORIGINAL_USERNAME = 'anycomment_social_original_username';

	/**
	 * Avatar hosted on website.
	 */
	const META_SOCIAL_AVATAR = 'anycomment_social_avatar';

	/**
	 * Avatar hosted on remote.
	 */
	const META_SOCIAL_AVATAR_ORIGIN = 'anycomment_social_avatar_origin';

	/**
	 * Cookie used to store errors.
	 */
	const COOKIE_ERROR_STORAGE = 'anycomment_errors';

	/**
	 * Associative array list of providers.
	 */
	protected static $providers = [
		self::SOCIAL_VKONTAKTE     => 'Vkontakte',
		self::SOCIAL_FACEBOOK      => 'Facebook',
		self::SOCIAL_TWITTER       => 'Twitter',
		self::SOCIAL_GOOGLE        => 'Google',
		self::SOCIAL_GITHUB        => 'GitHub',
		self::SOCIAL_ODNOKLASSNIKI => 'Odnoklassniki',
		self::SOCIAL_TWITCH        => 'TwitchTV',
		self::SOCIAL_DRIBBBLE      => 'Dribbble',
		self::SOCIAL_STEAM         => 'Steam',
		self::SOCIAL_YAHOO         => 'Yahoo',
		self::SOCIAL_YANDEX        => 'Yandex',
		self::SOCIAL_MAILRU        => 'Mailru',
		self::SOCIAL_WORDPRESS     => 'WordPress',
	];

	protected static $rest_prefix = 'anycomment';
	protected static $rest_version = 'v1';

	/**
	 * @var array Used to track list of requested avatar and the same email requested again, it will user existing check.
	 */
	protected static $avatarMap = [];

	/**
	 * AC_SocialAuth constructor.
	 */
	public function __construct() {
		// When only guests allowed, social should not be allowed
		if ( ! AnyCommentGenericSettings::is_form_type_guests() ) {
			$this->init_rest_route();
		}
	}

	/**
	 * Get REST namespace.
	 * @return string
	 */
	private static function get_rest_namespace() {
		return sprintf( '%s/%s', static::$rest_prefix, static::$rest_version );
	}

	/**
	 * Used to initiate REST routes to log in client, etc.
	 */
	private function init_rest_route() {
		add_action( 'rest_api_init', function () {
			$namespace = static::get_rest_namespace();
			$route     = '/auth/(?P<social>\w[\w\s]*)/';
			register_rest_route( $namespace, $route, [
				'methods'  => 'GET',
				'callback' => [ $this, 'process_socials' ],
			] );
		} );
	}

	/**
	 * Main method to process social-like request to authentication, etc.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 */
	public function process_socials( WP_REST_Request $request ) {
		$redirect        = $request->get_param( 'redirect' );
		$social          = $request->get_param( 'social' );
		$cookie_redirect = self::COOKIE_REDIRECT;

		if ( ! $this->social_exists( $social ) || is_user_logged_in() ) {
			wp_safe_redirect( $redirect );
			exit();
		}


		if ( $redirect !== null ) {
			setcookie( $cookie_redirect, $redirect, time() + 3600 );
		} else {
			$redirectCookie = array_key_exists( $cookie_redirect, $_COOKIE ) ?
				sanitize_text_field( $_COOKIE[ $cookie_redirect ] ) :
				null;

			if ( false !== strpos( $redirectCookie, 'wp-login.php' ) ) {
				$redirectCookie = home_url( '/wp-admin/' );
			} else {
				// Add GET param to bast cache on redirect back to post
				$get_params     = parse_url( $redirectCookie, PHP_URL_QUERY );
				$redirectCookie .= ( ! empty( $get_params ) ? '&' : '?' ) . 'ac=' . uniqid();

				// Add anchor
				$anchor = parse_url( $redirectCookie, PHP_URL_FRAGMENT );
				if ( $redirectCookie !== null && empty( $anchor ) ) {
					$redirectCookie .= '#comments';
				}
			}

			$redirect = $redirectCookie !== null ? $redirectCookie : '/' ;
		}

		try {
			$this->prepare_auth();
			$adapter = $this->authenticate( $social );
		} catch ( \Exception $exception ) {
			AnyCommentCore::logger()->error( sprintf(
				'Failed to login user on %s via "%s" social, exception: "%s", trace %s',
				$redirect,
				$social,
				$exception->getMessage(),
				$exception->getTraceAsString()
			) );

			wp_safe_redirect( $redirect );
			exit();
		}

		$user = $adapter->getUserProfile();

		if ( ! $user instanceof Profile ) {
			wp_safe_redirect( $redirect );
			exit();
		}

		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}

		$auth_result = $this->process_authentication( $user, $social );

		if ( $auth_result instanceof WP_Error ) {
			static::addError( $auth_result->get_error_message() );
		}

		if ( session_status() == PHP_SESSION_ACTIVE ) {
			session_destroy();
		}

		wp_safe_redirect( $redirect );
		exit();
	}

	/**
	 * Get provider name based on provided social key.
	 *
	 * @param string $social Social name, usually lowercase, which comes from REST API.
	 *
	 * @return null|string NULL when unable to get provider name.
	 */
	public function get_provider_name( $social ) {
		if ( ! $this->social_exists( $social ) ) {
			return null;
		}

		return trim( self::$providers[ $social ] );
	}

	/**
	 * Check whether social exists or not.
	 *
	 * @param string $social Social name to be checked for existance.
	 *
	 * @return bool
	 */
	public function social_exists( $social ) {
		return isset( self::$providers[ strtolower( $social ) ] );
	}

	/**
	 * Get verbal name based on provided social key.
	 *
	 * @param string $social Social media key.
	 *
	 * @return mixed
	 */
	public static function get_verbal_name( $social ) {
		$verbals = [
			self::SOCIAL_VKONTAKTE     => __( 'Vkontakte', 'anycomment' ),
			self::SOCIAL_FACEBOOK      => __( 'Facebook', 'anycomment' ),
			self::SOCIAL_TWITTER       => __( 'Twitter', 'anycomment' ),
			self::SOCIAL_GOOGLE        => __( 'Google', 'anycomment' ),
			self::SOCIAL_GITHUB        => __( 'GitHub', 'anycomment' ),
			self::SOCIAL_ODNOKLASSNIKI => __( 'Odnoklassniki', 'anycomment' ),
			self::SOCIAL_TWITCH        => __( 'Twitch', 'anycomment' ),
			self::SOCIAL_DRIBBBLE      => __( 'Dribbble', 'anycomment' ),
			self::SOCIAL_YAHOO         => __( 'Yahoo', 'anycomment' ),
			self::SOCIAL_YANDEX        => __( 'Yandex', 'anycomment' ),
			self::SOCIAL_MAILRU        => __( 'Mail.Ru', 'anycomment' ),
			self::SOCIAL_STEAM         => __( 'Steam', 'anycomment' ),
			self::SOCIAL_WORDPRESS     => __( 'WordPress', 'anycomment' ),
		];

		return isset( $verbals[ $social ] ) ?
			$verbals[ $social ] :
			$social;
	}

	/**
	 * Prepare authentication.
	 * @throws \Hybridauth\Exception\InvalidArgumentException on failure.
	 */
	private function prepare_auth() {
		$config = [
			'providers' => [
				self::$providers[ self::SOCIAL_VKONTAKTE ]     => [
					'enabled'  => AnyCommentSocialSettings::is_vk_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_vk_app_id(),
						'secret' => AnyCommentSocialSettings::get_vk_secure_key(),
					],
					'callback' => static::get_vk_callback(),
				],
				self::$providers[ self::SOCIAL_GOOGLE ]        => [
					'enabled'  => AnyCommentSocialSettings::is_google_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_google_client_id(),
						'secret' => AnyCommentSocialSettings::get_google_secret(),
					],
					'scope'    => 'profile https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read',
					'callback' => static::get_google_callback(),
				],
				self::$providers[ self::SOCIAL_FACEBOOK ]      => [
					'enabled'  => AnyCommentSocialSettings::is_facebook_active(),
					'scope'    => 'email, public_profile',
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_facebook_app_id(),
						'secret' => AnyCommentSocialSettings::get_facebook_app_secret(),
					],
					'callback' => static::get_facebook_callback(),
				],
				self::$providers[ self::SOCIAL_TWITTER ]       => [
					'enabled'  => AnyCommentSocialSettings::is_twitter_active(),
					'keys'     => [
						'key'    => AnyCommentSocialSettings::get_twitter_consumer_key(),
						'secret' => AnyCommentSocialSettings::get_twitter_consumer_secret(),
					],
					'callback' => static::get_twitter_callback(),
				],
				self::$providers[ self::SOCIAL_GITHUB ]        => [
					'enabled'  => AnyCommentSocialSettings::is_github_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_github_client_id(),
						'secret' => AnyCommentSocialSettings::get_github_secret_key(),
					],
					'callback' => static::get_github_callback(),
				],
				self::$providers[ self::SOCIAL_ODNOKLASSNIKI ] => [
					'enabled'  => AnyCommentSocialSettings::is_odnoklassniki_on(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_odnoklassniki_app_id(),
						'key'    => AnyCommentSocialSettings::get_odnoklassniki_app_key(),
						'secret' => AnyCommentSocialSettings::get_odnoklassniki_app_secret(),
					],
					'callback' => static::get_ok_callback(),
				],
				self::$providers[ self::SOCIAL_TWITCH ]        => [
					'enabled'  => AnyCommentSocialSettings::is_twitch_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_twitch_client_id(),
						'secret' => AnyCommentSocialSettings::get_twitch_client_secret(),
					],
					'callback' => static::get_twitch_callback(),
				],
				self::$providers[ self::SOCIAL_DRIBBBLE ]      => [
					'enabled'  => AnyCommentSocialSettings::is_dribbble_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_dribbble_client_id(),
						'secret' => AnyCommentSocialSettings::get_dribbble_client_secret(),
					],
					'callback' => static::get_dribbble_callback(),
				],

				self::$providers[ self::SOCIAL_STEAM ]  => [
					'enabled'  => AnyCommentSocialSettings::is_steam_active(),
					'keys'     => [
						'secret' => AnyCommentSocialSettings::get_steam_secret(),
					],
					'callback' => static::get_steam_callback(),
				],
				self::$providers[ self::SOCIAL_YANDEX ] => [
					'enabled'  => AnyCommentSocialSettings::is_yandex_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_yandex_client_id(),
						'secret' => AnyCommentSocialSettings::get_yandex_client_secret(),
					],
					'callback' => static::get_yandex_callback(),
				],
				self::$providers[ self::SOCIAL_MAILRU ] => [
					'enabled'  => AnyCommentSocialSettings::is_mailru_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_mailru_client_id(),
						'secret' => AnyCommentSocialSettings::get_mailru_client_secret(),
					],
					'callback' => static::get_mailru_callback(),
				],
				self::$providers[ self::SOCIAL_YAHOO ]  => [
					'enabled'  => AnyCommentSocialSettings::is_yahoo_active(),
					'keys'     => [
						'id'     => AnyCommentSocialSettings::get_yahoo_app_id(),
						'secret' => AnyCommentSocialSettings::get_yahoo_client_secret(),
					],
					'callback' => static::get_yahoo_callback(),
				],
			],
		];

		$this->_auth = new \Hybridauth\Hybridauth( $config );
	}

	/**
	 * Authenticate a user.
	 *
	 * @param string $social Social network name.
	 *
	 * @return bool|\Hybridauth\Adapter\AdapterInterface
	 * @throws \Hybridauth\Exception\InvalidArgumentException
	 * @throws \Hybridauth\Exception\UnexpectedValueException
	 */
	private function authenticate( $social ) {
		if ( $this->_auth === null ) {
			return false;
		}

		$providerName = $this->get_provider_name( $social );

		return $this->_auth->authenticate( $providerName );
	}

	/**
	 * Get vk callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_vk_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_VKONTAKTE, $redirect );
	}

	/**
	 * Get ok callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_ok_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_ODNOKLASSNIKI, $redirect );
	}

	/**
	 * Get Twitch callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_twitch_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_TWITCH, $redirect );
	}

	/**
	 * Get Dribble callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_dribbble_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_DRIBBBLE, $redirect );
	}

	/**
	 * Get Steam callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_steam_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_STEAM, $redirect );
	}

	/**
	 * Get Yandex callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_yandex_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_YANDEX, $redirect );
	}

	/**
	 * Get Mailru callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_mailru_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_MAILRU, $redirect );
	}

	/**
	 * Get Yahoo callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_yahoo_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_YAHOO, $redirect );
	}

	/**
	 * Get WordPress callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_wordpress_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_WORDPRESS, $redirect );
	}

	/**
	 * Get Google callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_google_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_GOOGLE, $redirect );
	}

	/**
	 * Get Github callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_github_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_GITHUB, $redirect );
	}

	/**
	 * Get Facebook callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_facebook_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_FACEBOOK, $redirect );
	}

	/**
	 * Get Twitter callback URL.
	 *
	 * @param null|string $redirect Redirect URL added to the link.
	 *
	 * @return string
	 */
	public static function get_twitter_callback( $redirect = null ) {
		return static::get_callback_url( self::SOCIAL_TWITTER, $redirect );
	}

	/**
	 * Get REST url + redirect url with it.
	 *
	 * @param string $social_type URL type, e.g. vk
	 * @param string|null $redirect Redirect URL where to send back user.
	 * @param bool $addHash If required to add #comments hash will allows to move users creen directly to comments section.
	 *
	 * @return string
	 */
	public static function get_callback_url( $social_type, $redirect = null, $addHash = false ) {
		$url = static::get_rest_namespace() . "/auth/" . $social_type;


		if ( $redirect !== null ) {

			$permalink = get_option( 'permalink_structure' );

			if ( empty( $permalink ) ) {
				$url .= '&';
			} else {
				$url .= '?';
			}

			$url .= "redirect=$redirect";
		}

		if ( $addHash ) {
			$url .= '#comments';
		}

		return rest_url( $url );
	}

	/**
	 * Process Google authorization.
	 *
	 * @param Profile $user User to be processed.
	 * @param string $social Social name.
	 *
	 * @return bool|WP_Error WP_Error on failure.
	 */
	private function process_authentication( $user, $social ) {
		$email = isset( $user->email ) && ! empty( $user->email ) ?
			$user->email :
			null;

		$full_name    = trim( $user->firstName . ' ' . $user->lastName );
		$display_name = ! empty( $full_name ) ? $full_name : $user->displayName;

		$userdata = [
			'display_name'  => $display_name,
			'user_nicename' => $user->firstName,
			'first_name'    => $user->firstName,
			'last_name'     => $user->lastName,
		];

		if ( strlen( $user->profileURL ) <= 100 && false !== strpos( $user->profileURL, 'http' ) ) {
			$userdata['user_url'] = $user->profileURL;
		}

		if ( $email !== null ) {
			$userdata['user_email'] = $email;
		}

		$searchBy    = $email !== null ? 'email' : 'login';
		$searchValue = $email !== null ? $email : $user->identifier;

		return $this->auth_user(
			$user,
			$social,
			$searchBy,
			$searchValue,
			$userdata,
			[
				self::META_SOCIAL_LINK          => $user->profileURL,
				self::META_SOCIAL_AVATAR_ORIGIN => $user->photoURL,
			]
		);
	}


	/**
	 * Authenticate user with additional check whether such
	 * user already exists or not. When user does not exist,
	 * it create it. When exists, uses existing information to authorize.
	 *
	 * @param Profile $user_profile User profile from library.
	 * @param string $social Social type. e.g. twitter.
	 * @param string $field Field name to be used to check existence of such user.
	 * @param string $value Value of the $field to be checked for.
	 * @param array $user_data User data to be created for user, when does not exist.
	 * @param array $user_meta User meta data to be create for user, when does not exist.
	 *
	 * @return bool|WP_Error
	 * @see AnyCommentSocialAuth::get_user_by() for more information about $field and $value fields.
	 *
	 */
	public function auth_user( $user_profile, $social, $field, $value, array $user_data, array $user_meta ) {

		// Transliterate display name into username, so it is looks
		// more friendly, as previously it was as "social_username"
		// which does look nice when seeing posts by user - e.g. website.com/facebook_username
		$prepared_login = AnyCommentUser::prepare_login( $user_data['display_name'] );

		// Need to make username unique per social network, otherwise
		// some of the IDs might collide at some point
		// format: {social_name}_{user_identity} where:
		// - social_name is lowercased social name
		// - user_identity is usually ID from social or verbose name which usually permanent and unchanged
		$user_meta[ self::META_SOCIAL_ORIGINAL_USERNAME ] = sprintf( '%s_%s', $social, $user_profile->identifier );

		if ( $field === 'login' ) {
			$value = $user_meta[ self::META_SOCIAL_ORIGINAL_USERNAME ];
		}

		if ( ! isset( $user_data['user_login'] ) ) {
			$user_data['user_login'] = $prepared_login;
		}

		if ( ! isset( $user_data['role'] ) ) {
			$user_data['role'] = AnyCommentGenericSettings::get_register_default_group();
		}

		// Preset social type as it is passed anyways in the method
		if ( ! isset( $user_meta[ self::META_SOCIAL_TYPE ] ) ) {
			$user_meta[ self::META_SOCIAL_TYPE ] = $social;
		}

		$user = $this->get_user_by( $field, $value );

		if ( $user !== false && ! AnyCommentUserMeta::is_social_login( $user ) ) {
			return new WP_Error( 'use_wordpress_to_login', __( "Please use normal login form, as this email is associated with a WordPress user.", "anycomment" ) );
		}

		$found_user_social = AnyCommentUserMeta::get_social_type( $user );

		if ( $found_user_social !== null && $field === 'email' && $found_user_social !== $social ) {
			return new WP_Error( 'use_original_social_account', sprintf( __( "Please use %s to login as this %s seems to be already taken.", "anycomment" ),
				static::get_verbal_name( $found_user_social ),
				( $field === 'email' ? __( "email", 'anycomment' ) : __( "login", 'anycomment' ) )
			) );
		}

		if ( $user === false ) {

			if ( ! isset( $user_meta[ self::META_SOCIAL_AVATAR ] ) ) {
				$user_meta[ self::META_SOCIAL_AVATAR ] = $this->upload_photo( $social, $user_profile );
			}

			$newUserId = $this->insert_user( $user_data, $user_meta );

			if ( ! $newUserId ) {
				return false;
			}

			$userId = $newUserId;
		} else {
			$userId = $user->ID;
			$this->update_user_meta( $user->ID, $user_meta );
		}

		wp_clear_auth_cookie();
		wp_set_current_user( $userId );
		wp_set_auth_cookie( $userId, true );

		/**
		 * Fires after user successfully logged in.
		 *
		 * @param WP_User $user Instance of user being logged in.
		 * @param string $cookie_post_url URL to post retrieved from cookie. NULL when unable to get post URL.
		 *
		 * @since 0.0.76
		 *
		 * @package
		 */
		$cookie_post_url = array_key_exists( self::COOKIE_REDIRECT, $_COOKIE ) ? sanitize_text_field( $_COOKIE[ self::COOKIE_REDIRECT ] ) : null;
		do_action( 'anycomment/user/logged_in', $user, $cookie_post_url );

		return true;
	}

	/**
	 * Upload photo locally from social.
	 *
	 * @param string $social Social name.
	 * @param Profile $user_profile User profile.
	 *
	 * @return string|null string on success, null on failure.
	 */
	public function upload_photo( $social, $user_profile ) {
		$photoUrl = null;

		if ( ! empty( $user_profile->photoURL ) ) {
			$localUrl = AnyCommentUploadHandler::upload_avatar( $user_profile->photoURL, [
				$social,
				$user_profile->identifier,
			] );

			if ( $localUrl !== false ) {
				$photoUrl = $localUrl;
			}
		}

		return $photoUrl;
	}

	public function is_user_from_social( $user, $social ) {
		$social_from_meta = trim( get_user_meta( $user->ID, self::META_SOCIAL_TYPE, true ) );
		$social           = trim( $social );

		if ( $social_from_meta !== $social ) {
			return false;
		}

		return true;
	}


	/**
	 * Check weather user exists.
	 *
	 * @param string $field Field to check for user existence (e.g. username).
	 * @param string $value Fields to check for value of $field.
	 * @param string $social Name of the social.
	 *
	 * @return false|WP_User false on failure.
	 */
	public function get_user_by( $field, $value, $social = null ) {

		$user = false;

		if ( $field === 'email' ) {
			$user = get_user_by( $field, $value );
		} elseif ( $field === 'login' ) {
			global $wpdb;

			/**
			 * Try to find user by unique username in the user meta
			 */
			$prepared_sql = $wpdb->prepare( "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s", [
				self::META_SOCIAL_ORIGINAL_USERNAME,
				$value,
			] );
			$user_meta    = $wpdb->get_row( $prepared_sql );

			if ( empty( $user_meta ) ) {
				return false;
			}

			// Try to get user by ID from meta
			$user = get_user_by( 'id', $user_meta->user_id );
		}

		if ( $user === false ) {
			return false;
		}

		if ( $social !== null ) {
			$social_from_meta = AnyCommentUserMeta::get_social_type( $user );
			$social           = trim( $social );

			if ( $social_from_meta !== $social ) {
				return false;
			}
		}

		return $user;
	}

	/**
	 * @param array $user_data See options below:
	 * - user_login
	 * - user_email
	 * - display_name
	 * - user_nicename
	 * - user_url
	 * @param array $user_meta User meta to be added when `$userdata` added successfully.
	 *
	 * @return false|int false on failure, int on success.
	 *
	 * @see wp_generate_password() for how password is being generated.
	 * @see wp_insert_user() for how `$userdata` param is being processed.
	 * @see add_user_meta() for how `$usermeta` param is being processed.
	 */
	public function insert_user( $user_data, $user_meta ) {
		if ( ! isset( $user_data['userpass'] ) ) {
			// Generate some random password for user
			$user_data['user_pass'] = wp_generate_password( 12, true );
		}

		$newUserId = wp_insert_user( $user_data );

		if ( $newUserId instanceof WP_Error ) {
			return false;
		}

		$metaInsertCount = 0;
		foreach ( $user_meta as $key => $value ) {
			if ( add_user_meta( $newUserId, $key, $value ) ) {
				$metaInsertCount ++;
			}
		}

		// If number of inserted metas is less then was requested to add
		if ( $metaInsertCount < count( $user_meta ) ) {
			return false;
		}

		return $newUserId;
	}

	/**
	 * Update user meta by ID.
	 *
	 * @param int $user_id User's ID.
	 * @param array $usermeta List of user meta to be updated.
	 *
	 * @return bool
	 * @see update_user_meta() when meta does not exist, it will be added. See for more info.
	 *
	 */
	public function update_user_meta( $user_id, $usermeta ) {
		if ( ! get_user_by( 'id', $user_id ) || ! is_array( $usermeta ) || ! empty( $usermeta ) ) {
			return false;
		}

		$metaUpdateCount = 0;
		foreach ( $usermeta as $key => $value ) {
			if ( update_user_meta( $user_id, $key, $value ) ) {
				$metaUpdateCount ++;
			}
		}

		// If number of inserted metas is less then was requested to add
		if ( $metaUpdateCount < count( $usermeta ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get current user avatar URL.
	 *
	 * @return string|null NULL returned when user does not have any avatar.
	 */
	public function get_active_user_avatar_url() {
		$active_user = $user = wp_get_current_user();
		if ( $active_user instanceof WP_User ) {

			return static::get_user_avatar_url( $user->ID );
		}

		return null;
	}

	/**
	 * Get user social type.
	 *
	 * @param int $user_id User ID to be checked social type for.
	 *
	 * @return mixed
	 */
	public static function get_user_social_type( $user_id ) {
		return get_user_meta( $user_id, 'anycomment_social', true );
	}

	/**
	 * Get user avatar by user id.
	 *
	 * @param mixed $id_or_email User ID, user instance, comment instance or email to be searched for.
	 *
	 * @return mixed|null
	 */
	public static function get_user_avatar_url( $id_or_email ) {

		$avatar_cache = UserCache::getAvatar( $id_or_email );
		$can_cache    = $avatar_cache !== null;

		if ( $can_cache && $avatar_cache->isHit() ) {
			return $avatar_cache->get();
		}

		$user_id = null;
		$email   = null;

		if ( is_numeric( $id_or_email ) ) {
			$user_id = $id_or_email;
			$user    = get_user_by( 'id', $user_id );

			if ( $user !== false ) {
				$email = $user->user_email;
			}
		} elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) !== false ) {
			$user_id = $user->ID;
			$email   = $id_or_email;
		} elseif ( $id_or_email instanceof WP_User ) {
			$user_id = $id_or_email->ID;
			$email   = $id_or_email->user_email;
		} elseif ( $id_or_email instanceof WP_Comment ) {

			if ( (int) $id_or_email->user_id !== 0 ) {
				$user_id = $id_or_email->user_id;
			}

			$email = $id_or_email->comment_author_email;
		}

		$avatar_url = '';

		$cache_time = 60;

		/**
		 * When WP User avatar is active, user has Gravatar and has user avatar within
		 * the plugin itself, return the plugin's version.
		 */
		if ( empty( $avatar_url ) &&
		     AnyCommentIntegrationSettings::is_wp_user_avatar_active() &&
		     function_exists( 'has_wp_user_avatar' ) &&
		     function_exists( 'get_wp_user_avatar_src' ) ) {
			if ( has_wp_user_avatar( $email ) ) {
				$avatar_url = get_wp_user_avatar_src( $id_or_email );
			}
		}

		if ( empty( $avatar_url ) && $user_id !== null ) {
			$social_avatar_url = AnyCommentUserMeta::get_social_avatar( $user_id );

			if ( ! empty( $social_avatar_url ) ) {
				$avatar_url = $social_avatar_url;
				if ( $can_cache ) {
					$cache_time = 24 * 60 * 60; // 1 day
				}
			}
		}

		/**
		 * As user does not have social avatar, it is required to che
		 * whether user has non-default gravatar avatar, if does return,
		 * otherwise get default plugin's one.
		 */
		if ( empty( $avatar_url ) && static::has_gravatar( $email, true ) ) {
			$avatar_url = get_avatar_url( $id_or_email, [ 'size' => AnyCommentAvatars::DEFAULT_AVATAR_WIDTH ] );
			if ( $can_cache ) {
				/**
				 * Base on the header information from Gravatar, they put 5 mins cache on all avatars,
				 * so why not using the same time.
				 * @link https://meta.stackexchange.com/a/37994
				 */
				$cache_time = 5 * 60;
			}
		}

		if ( empty( $avatar_url ) && ! AnyCommentGenericSettings::is_default_avatar_anycomment() ) {
			$avatar_url = get_avatar_url( $id_or_email, [
				'size'    => AnyCommentAvatars::DEFAULT_AVATAR_WIDTH,
				'default' => AnyCommentGenericSettings::get_default_avatar(),
			] );
		}

		if ( empty( $avatar_url ) ) {
			$avatar_url = apply_filters( 'anycomment/user/no_avatar', AnyComment()->plugin_url() . '/assets/img/no-avatar.png' );
		}

		if ( $can_cache ) {
			$avatar_cache->expiresAfter( $cache_time );
			$avatar_cache->set( $avatar_url )->save();
		}

		return $avatar_url;
	}

	/**
	 * Utility function to check if a gravatar exists for a given email or id
	 *
	 * @param int|string|object $id_or_email A user ID,  email address, or comment object
	 * @param bool $skip_checks Whether to skip checks for user email or not as it could have been previously checked
	 * already.
	 *
	 * @return bool if the gravatar exists or not
	 */
	public static function has_gravatar( $id_or_email, $skip_checks = false ) {

		$cache_key = 'anycomment/has-gravatar/' . md5( serialize( $id_or_email ) );

		$has_gravatar = wp_cache_get( $cache_key );

		if ( false !== $has_gravatar ) {
			return $has_gravatar;
		}

		//id or email code borrowed from wp-includes/pluggable.php
		$email = '';

		// When checks should be skipped, setting email itself
		if ( $skip_checks ) {
			$email = $id_or_email;
		} else {
			if ( is_numeric( $id_or_email ) ) {
				$id   = (int) $id_or_email;
				$user = get_userdata( $id );
				if ( $user ) {
					$email = $user->user_email;
				}
			} elseif ( is_object( $id_or_email ) ) {
				// No avatar for pingbacks or trackbacks
				$allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
				if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) ) {
					return false;
				}

				if ( ! empty( $id_or_email->user_id ) ) {
					$id   = (int) $id_or_email->user_id;
					$user = get_userdata( $id );
					if ( $user ) {
						$email = $user->user_email;
					}
				} elseif ( ! empty( $id_or_email->comment_author_email ) ) {
					$email = $id_or_email->comment_author_email;
				}
			} else {
				$email = $id_or_email;
			}
		}

		$email = strtolower( trim( $email ) );

		// Build the Gravatar URL by hasing the email address
		$url = 'http://www.gravatar.com/avatar/' . md5( $email ) . '?d=404';

		// Now check the headers...
		$headers = @get_headers( $url );

		// If 200 is found, the user has a Gravatar; otherwise, they don't.
		$hasGravatar = ! preg_match( '|200|', $headers[0] ) ? false : true;

		wp_cache_set( $cache_key, $has_gravatar );

		return $hasGravatar;
	}

	/**
	 * Get list of errors stored in cookies.
	 *
	 * @param bool $decode If required to return decoded JSON.
	 * @param bool $andClean Will clean cookie before errors returned.
	 *
	 * @return string|array|null
	 */
	public static function getErrors( $decode = true, $andClean = false ) {
		$errors = array_key_exists( self::COOKIE_ERROR_STORAGE, $_COOKIE ) ? sanitize_text_field( $_COOKIE[ self::COOKIE_ERROR_STORAGE ] ) : null;

		if ( $errors === null ) {
			return null;
		}

		if ( $andClean ) {
			static::cleanErrors();
		}

		if ( $decode ) {
			return json_decode( stripslashes( $errors ) );
		}

		return $errors;
	}

	/**
	 * Add new error.
	 *
	 * @param string $errorMessage Error message to be added.
	 *
	 * @return bool
	 */
	public function addError( $errorMessage ) {

		$errors = static::getErrors();

		if ( $errors !== null ) {
			$errors[] = $errorMessage;
		} else {
			$errors   = [];
			$errors[] = $errorMessage;
		}

		$errorsJson = json_encode( $errors, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE );

		return setcookie( self::COOKIE_ERROR_STORAGE, $errorsJson, 0, "/" );
	}

	/**
	 * Clean cookie errors.
	 */
	public static function cleanErrors() {
		if ( static::getErrors( false ) !== null ) {
			setcookie( self::COOKIE_ERROR_STORAGE, null, - 1, '/' );
		}
	}
}
