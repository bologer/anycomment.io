<?php

namespace AnyComment\Repository;

class CommentRepository implements CommentRepositoryInterface {
	public function findByPostAndConditions( int $postId, int $limit = 30, $offset = 0 ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d ORDER BY comment_date ASC LIMIT %d OFFSET %d", $postId, $limit, $offset ),
			ARRAY_A
		);
	}

	public function countByPostId( int $postId ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d", $postId ) );

		return intval( $count );
	}
}
