<?php

namespace AnyComment;

use AnyComment\Admin\AnyCommentSocialSettings;
use AnyComment\Rest\AnyCommentSocialAuth;

/**
 * Class AnyCommentSocials is having shared method regarding socials.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment
 */
class AnyCommentSocials {

	/**
	 * Get list of socials.
	 *
	 * @param string|null $redirectUrl URL back to redirect on success or failure.
	 *
	 * @return array|null List of socials or NULL in case when no socials defined or so.
	 */
	public static function get_all( $redirectUrl = null ) {
		$socials = [
			AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_VKONTAKTE,
				'url'     => AnyCommentSocialAuth::get_vk_callback( $redirectUrl ),
				'label'   => __( 'VK', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_vk_active()
			],
			AnyCommentSocialAuth::SOCIAL_TWITTER       => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_TWITTER,
				'url'     => AnyCommentSocialAuth::get_twitter_callback( $redirectUrl ),
				'label'   => __( 'Twitter', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_twitter_active()
			],
			AnyCommentSocialAuth::SOCIAL_FACEBOOK      => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_FACEBOOK,
				'url'     => AnyCommentSocialAuth::get_facebook_callback( $redirectUrl ),
				'label'   => __( 'Facebook', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_facebook_active()
			],
			AnyCommentSocialAuth::SOCIAL_GOOGLE        => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_GOOGLE,
				'url'     => AnyCommentSocialAuth::get_google_callback( $redirectUrl ),
				'label'   => __( 'Google', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_google_active()
			],
			AnyCommentSocialAuth::SOCIAL_GITHUB        => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_GITHUB,
				'url'     => AnyCommentSocialAuth::get_github_callback( $redirectUrl ),
				'label'   => __( 'Github', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_github_active()
			],
			AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI,
				'url'     => AnyCommentSocialAuth::get_ok_callback( $redirectUrl ),
				'label'   => __( 'Odnoklassniki', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_odnoklassniki_on()
			],
			AnyCommentSocialAuth::SOCIAL_INSTAGRAM     => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_INSTAGRAM,
				'url'     => AnyCommentSocialAuth::get_instagram_callback( $redirectUrl ),
				'label'   => __( 'Instagram', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_instagram_active()
			],
			AnyCommentSocialAuth::SOCIAL_TWITCH        => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_TWITCH,
				'url'     => AnyCommentSocialAuth::get_twitch_callback( $redirectUrl ),
				'label'   => __( 'Twitch', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_twitch_active()
			],
			AnyCommentSocialAuth::SOCIAL_DRIBBBLE      => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_DRIBBBLE,
				'url'     => AnyCommentSocialAuth::get_dribbble_callback( $redirectUrl ),
				'label'   => __( 'Dribbble', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_dribbble_active()
			],
			AnyCommentSocialAuth::SOCIAL_YANDEX        => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_YANDEX,
				'url'     => AnyCommentSocialAuth::get_yandex_callback( $redirectUrl ),
				'label'   => __( 'Yandex', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_yandex_active(),
			],
			AnyCommentSocialAuth::SOCIAL_MAILRU        => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_MAILRU,
				'url'     => AnyCommentSocialAuth::get_mailru_callback( $redirectUrl ),
				'label'   => __( 'Mail.Ru', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_mailru_active(),
			],
			AnyCommentSocialAuth::SOCIAL_STEAM         => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_STEAM,
				'url'     => AnyCommentSocialAuth::get_steam_callback( $redirectUrl ),
				'label'   => __( 'Steam', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_steam_active(),
			],
			AnyCommentSocialAuth::SOCIAL_YAHOO         => [
				'slug'    => AnyCommentSocialAuth::SOCIAL_YAHOO,
				'url'     => AnyCommentSocialAuth::get_yahoo_callback( $redirectUrl ),
				'label'   => __( 'Yahoo', "anycomment" ),
				'visible' => AnyCommentSocialSettings::is_yahoo_active()
			],
		];


		$wordpress_login_url = site_url( 'wp-login.php', 'login' );

		if ( ! empty( $redirectUrl ) ) {
			$redirectUrl         .= '#comments';
			$wordpress_login_url = add_query_arg( 'redirect_to', urlencode( $redirectUrl ), $wordpress_login_url );
		}

		$socials[ AnyCommentSocialAuth::SOCIAL_WORDPRESS ] = [
			'slug'    => 'wordpress',
			'url'     => $wordpress_login_url,
			'label'   => __( "WordPress", 'anycomment' ),
			'visible' => AnyCommentSocialSettings::is_wordpress_native_active()
		];

		$socials = apply_filters( 'anycomment_get_socials', $socials, $socials );

		if ( count( $socials ) <= 0 ) {
			return null;
		}

		return $socials;
	}
}