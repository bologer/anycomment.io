<?php

namespace AnyComment\Domain\Comment;

use Webmozart\Assert\Assert;

class CreateComment {
	private $authorId;
	private $commentText;
	private $parentId;
	private $postId;

	public function __construct( $authorId, $postId, $commentText, $parentId ) {
		Assert::integer( $authorId );
		Assert::integer( $postId );
		Assert::greaterThan( 0, $authorId );
		Assert::greaterThan( 0, $postId );
		Assert::stringNotEmpty( $commentText );
		Assert::nullOrInteger( $parentId );
		$this->authorId    = $authorId;
		$this->commentText = $commentText;
		$this->parentId    = $parentId;
		$this->postId      = $postId;
	}

	/**
	 * @return mixed
	 */
	public function getPostId() {
		return $this->postId;
	}

	/**
	 * @return mixed
	 */
	public function getAuthorId() {
		return $this->authorId;
	}

	/**
	 * @return mixed
	 */
	public function getCommentText() {
		return $this->commentText;
	}

	/**
	 * @return mixed
	 */
	public function getParentId() {
		return $this->parentId;
	}
}
