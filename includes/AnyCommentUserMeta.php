<?php

namespace AnyComment;

use WP_User;
use AnyComment\Rest\AnyCommentSocialAuth;

class AnyCommentUserMeta {

	/**
	 * Get social avatar. This is cropped version.
	 *
	 * @param WP_User|int $user User instance or ID to be checked for.
	 *
	 * @return mixed
	 */
	public static function get_social_avatar( $user ) {
		if ( $user instanceof WP_User ) {
			$user_id = $user->ID;
		} else {
			$user_id = $user;
		}

		return get_user_meta( $user_id, AnyCommentSocialAuth::META_SOCIAL_AVATAR, true );
	}

	/**
	 * Get social type. e.g. vkontakte
	 *
	 * @param WP_User|int $user User instance or ID to be checked for.
	 *
	 * @return mixed
	 */
	public static function get_social_type( $user ) {

		if ( $user instanceof WP_User ) {
			$user_id = $user->ID;
		} else {
			$user_id = $user;
		}

		if ( empty( $user_id ) ) {
			return null;
		}

		return get_user_meta( $user_id, AnyCommentSocialAuth::META_SOCIAL_TYPE, true );
	}

	/**
	 * Check whether user logged in using social network or not.
	 *
	 * @param WP_User|int $user User instance or ID to be checked for.
	 *
	 * @return bool
	 */
	public static function is_social_login( $user ) {
		$socialType = static::get_social_type( $user );

		return $socialType !== null && ! empty( $socialType );
	}

	/**
	 * Get social profile URL.
	 *
	 * @param WP_User|int $user User instance or ID to be checked for.
	 * @param bool $html If required to return HTML link
	 *
	 * @return null|string
	 */
	public static function get_social_profile_url( $user, $html = false ) {

		if ( ! static::is_social_login( $user ) ) {
			return null;
		}

		$url = get_user_meta( $user, AnyCommentSocialAuth::META_SOCIAL_LINK, true );

		if ( empty( $url ) || strpos( $url, 'http' ) !== false ) {
			$url = null;
		}

		if ( empty( $url ) && ( $user_data = get_userdata( $user ) ) !== false ) {
			$url = $user_data->user_url;
		}

		return ! $html ? $url : sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', $url, $url );
	}
}
