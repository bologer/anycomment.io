<?php

class AnyCommentUser {

	/**
	 * Get user prepared for react output.
	 *
	 * @return null|WP_User
	 */
	public static function getSafeUser() {
		$user = wp_get_current_user();

		if ( $user->ID == 0 ) {
			return null;
		}

		unset( $user->data->user_activation_key );
		unset( $user->data->user_email );
		unset( $user->data->user_pass );
		unset( $user->data->user_registered );
		unset( $user->data->user_status );

		$user->user_avatar = AnyComment()->auth->get_user_avatar_url( $user->ID );

		return $user;
	}
}