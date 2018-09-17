<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get template part.
 *
 * @param string $templateName Name of the template to get.
 *
 * @return mixed
 */
function anycomment_get_template( $templateName ) {
	ob_start();
	include ANYCOMMENT_ABSPATH . "templates/$templateName.php";
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

/**
 * Display list of available login methods.
 *
 * @param bool $html If required to return rendered HTML or as array.
 * @param string $redirectUrl Redirect link after successful/failed authentication.
 *
 * @return string|array|null HTML formatted list (when $html false and array when true) of social links.
 */
function anycomment_login_with( $html = false, $redirectUrl = null ) {
	$socials = [
		AnyCommentSocialAuth::SOCIAL_VKONTAKTE     => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_VKONTAKTE,
			'url'     => AnyCommentSocialAuth::get_vk_callback( $redirectUrl ),
			'label'   => __( 'VK', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isVkOn()
		],
		AnyCommentSocialAuth::SOCIAL_TWITTER       => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_TWITTER,
			'url'     => AnyCommentSocialAuth::get_twitter_callback( $redirectUrl ),
			'label'   => __( 'Twitter', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isTwitterOn()
		],
		AnyCommentSocialAuth::SOCIAL_FACEBOOK      => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_FACEBOOK,
			'url'     => AnyCommentSocialAuth::get_facebook_callback( $redirectUrl ),
			'label'   => __( 'Facebook', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isFbOn()
		],
		AnyCommentSocialAuth::SOCIAL_GOOGLE        => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_GOOGLE,
			'url'     => AnyCommentSocialAuth::get_google_callback( $redirectUrl ),
			'label'   => __( 'Google', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isGoogleOn()
		],
		AnyCommentSocialAuth::SOCIAL_GITHUB        => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_GITHUB,
			'url'     => AnyCommentSocialAuth::get_github_callback( $redirectUrl ),
			'label'   => __( 'Github', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isGithubOn()
		],
		AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_ODNOKLASSNIKI,
			'url'     => AnyCommentSocialAuth::get_ok_callback( $redirectUrl ),
			'label'   => __( 'Odnoklassniki', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isOkOn()
		],
		AnyCommentSocialAuth::SOCIAL_INSTAGRAM     => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_INSTAGRAM,
			'url'     => AnyCommentSocialAuth::get_instagram_callback( $redirectUrl ),
			'label'   => __( 'Instagram', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isInstagramOn()
		],
		AnyCommentSocialAuth::SOCIAL_TWITCH        => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_TWITCH,
			'url'     => AnyCommentSocialAuth::get_twitch_callback( $redirectUrl ),
			'label'   => __( 'Twitch', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isTwitchOn()
		],
		AnyCommentSocialAuth::SOCIAL_DRIBBBLE      => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_DRIBBBLE,
			'url'     => AnyCommentSocialAuth::get_dribbble_callback( $redirectUrl ),
			'label'   => __( 'Dribbble', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isDribbbleOn()
		],
		AnyCommentSocialAuth::SOCIAL_YAHOO         => [
			'slug'    => AnyCommentSocialAuth::SOCIAL_YAHOO,
			'url'     => AnyCommentSocialAuth::get_yahoo_callback( $redirectUrl ),
			'label'   => __( 'Yahoo', "anycomment" ),
			'visible' => AnyCommentSocialSettings::isYahooOn()
		],
		'wordpress'                                => [

			'url' => wp_login_url()
		]
	];


	$wordpress_login_url = site_url( 'wp-login.php', 'login' );

	if ( ! empty( $redirectUrl ) ) {
		$redirectUrl         .= '#comments';
		$wordpress_login_url = add_query_arg( 'redirect_to', urlencode( $redirectUrl ), $wordpress_login_url );
	}

	$socials['wordpress'] = [
		'slug'    => 'wordpress',
		'url'     => $wordpress_login_url,
		'label'   => __( "WordPress", 'anycomment' ),
		'visible' => true
	];

	if ( count( $socials ) <= 0 ) {
		return null;
	}

	if ( ! $html ) {
		return $socials;
	}

	foreach ( $socials as $key => $social ):
		if ( ! $social['visible'] ) {
			continue;
		}
		?>
        <li><a href="<?= $social['url'] ?>"
               target="_parent"
               title="<?= $social['label'] ?>"
               class="<?= AnyComment()->classPrefix() ?>login-with-list-<?= $key ?>"><img
                        src="<?= AnyComment()->plugin_url() ?>/assets/img/icons/auth/social-<?= $key ?>.svg"
                        alt="<?= $social['label'] ?>"></a>
        </li>
	<?php
	endforeach;
}

add_action( 'anycomment_login_with', 'anycomment_login_with' );






