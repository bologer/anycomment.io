<?php


namespace AnyComment\Repository;


interface UserRepositoryInterface {
	/**
	 * Get list of comments by IDs.
	 *
	 * @param array $ids Non-associative comment id list.
	 *
	 * @return array List of found users.
	 */
	public function findByIds( array $ids );
}
