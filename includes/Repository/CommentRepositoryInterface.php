<?php

namespace AnyComment\Repository;

interface CommentRepositoryInterface {
	/**
	 * Get list of comments by given post and conditions.
	 *
	 * @param int $postId
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	public function findByPostAndConditions( int $postId, int $limit = 30, $offset = 0 );

	/**
	 * Get number of posts.
	 *
	 * @param int $postId
	 *
	 * @return int
	 */
	public function countByPostId( int $postId );
}