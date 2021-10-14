<?php

namespace AnyComment\Domain\Comment;

class CommentService {
	/**
	 * Create new comment.
	 *
	 * @param CreateComment $comment
	 *
	 * @return int
	 */
	public function create( CreateComment $comment ) {

		$commentDate = ( new \DateTime() )->format( 'Y-m-d H:i:s' );

		$commentId = wp_insert_comment( [
			'comment_author'       => '',
			'comment_author_email' => '',
			'comment_author_IP'    => '',

			'comment_date'     => $commentDate,
			'comment_date_gmt' => $commentDate,
			'comment_post_ID'  => $comment->getPostId(),
			'comment_content'  => $comment->getCommentText(),
			'comment_type'     => 'comment',
			'comment_parent'   => $comment->getParentId(),
		] );

		if ( $commentId === false ) {
			throw new \DomainException( 'Failed to create new comment' );
		}

		return $commentId;
	}
}
