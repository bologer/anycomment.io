<?php


namespace AnyComment\Repository;


use WP_User;

interface UserRepositoryInterface {
	/**
	 * Get list of comments by IDs.
	 *
	 * @param array $ids Non-associative comment id list.
	 *
	 * @return array List of found users.
	 */
	public function findByIds( array $ids );

	/**
	 * Find user by email.
	 *
	 * @param $email
	 *
	 * @return WP_User|null
	 */
	public function findByEmail( $email );

	/**
	 * Find user by email.
	 *
	 * @param string $socialSlug Social slug, such as "facebook".
	 * @param string|int $socialId Social ID, normally unique ID from social network.
	 *
	 * @return WP_User|null
	 */
	public function findBySocialSlugAndId( $socialSlug, $socialId );
}
