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
	 *
	 * @return null|string
	 */
	public static function getSocialProfileUrl( $user_id ) {
		if ( ! static::isSocialLogin( $user_id ) ) {
			return null;
		}

		if ( ( $user = get_userdata( $user_id ) ) !== false ) {
			return $user->user_url;
		}

		return null;
	}
}