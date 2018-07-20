<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/17/18
 * Time: 8:34 PM
 */

class AnyCommentUserMeta {

	/**
	 * Get social type. e.g. vkontakte
	 *
	 * @param int $user_id User ID to be checked for.
	 *
	 * @return mixed
	 */
	public static function getSocialType( $user_id ) {
		if ( empty( $user_id ) ) {
			return null;
		}

		return get_user_meta( $user_id, 'anycomment_social', true );
	}

	/**
	 * Check whether user logged in using social network or not.
	 *
	 * @param int $user_id User ID to be checked for.
	 *
	 * @return bool
	 */
	public static function isSocialLogin( $user_id ) {
		$socialType = static::getSocialType( $user_id );

		return $socialType !== null && ! empty( $socialType );
	}

	/**
	 * Get social profile URL.
	 *
	 * @param int $user_id User ID to be checked for.
	 * @param bool $html If required to return HTML link
	 *
	 * @return null|string
	 */
	public static function getSocialProfileUrl( $user_id, $html = false ) {
		if ( ! static::isSocialLogin( $user_id ) ) {
			return null;
		}

		$url = get_user_meta( $user_id, AnyCommentSocialAuth::META_SOCIAL_LINK, true );

		if ( empty( $url ) || strpos( $url, 'http' ) !== false ) {
			$url = null;
		}

		if ( empty( $url ) && ( $user = get_userdata( $user_id ) ) !== false ) {
			$url = $user->user_url;
		}

		return ! $html ? $url : sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', $url, $url );
	}


}