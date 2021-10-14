<?php

namespace AnyComment\Repository;

use WP_User;

class UserRepository implements UserRepositoryInterface {
	public function findByIds( array $ids ) {
		global $wpdb;
		foreach ( $ids as $id ) {
			if ( ! is_int( $id ) ) {
				throw new \InvalidArgumentException( 'Only numeric values allowed.' );
			}
		}
		$ids = implode( ',', $ids );

		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $wpdb->users WHERE ID IN ($ids)" ),
			ARRAY_A
		);
	}

	/**
	 * @inheritDoc
	 */
	public function findByEmail( $email ) {
		$user = get_user_by( 'email', $email );
		if ( $user === false ) {
			return null;
		}

		return $user;
	}

	/**
	 * @inheritDoc
	 */
	public function findBySocialSlugAndId( $socialSlug, $socialId ) {
		global $wpdb;
		$preparedSql = $wpdb->prepare( "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s", [
			'anycomment_social_original_username',
			sprintf( '%s_%s', $socialSlug, $socialId ),
		] );

		$userMeta = $wpdb->get_row( $preparedSql );

		if ( empty( $userMeta ) ) {
			return null;
		}

		$user = get_user_by( 'id', $userMeta->user_id );
		if ( $user === false ) {
			return null;
		}

		return $user;
	}
}
