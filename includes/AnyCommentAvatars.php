<?php

namespace AnyComment;

use AnyComment\Rest\AnyCommentSocialAuth;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentAvatars is used as helper to manage avatars.
 */
class AnyCommentAvatars {

	/**
	 * Default avatar width.
	 */
	const DEFAULT_AVATAR_WIDTH = 60;

	/**
	 * Default avatar height.
	 */
	const DEFAULT_AVATAR_HEIGHT = 60;


	/**
	 * AnyCommentAvatars constructor.
	 *
	 * Allow to override all avatars in admin.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'get_avatar', [ $this, 'override_avatar_globally' ], 10, 5 );
		}
	}


	/**
	 * Override avatar globally.
	 *
	 * @param $avatar_html
	 * @param $id_or_email
	 * @param $size
	 * @param $default
	 * @param $alt
	 *
	 * @return string
	 */
	public function override_avatar_globally( $avatar_html, $id_or_email, $size, $default, $alt ) {
		return sprintf( "<img alt=\"%s\" src=\"%s\" class=\"avatar avatar-%s photo\" height=\"%s\" width=\"%s\" />",
			$alt,
			AnyCommentSocialAuth::get_user_avatar_url( $id_or_email ),
			$size,
			$size,
			$size
		);
	}
}