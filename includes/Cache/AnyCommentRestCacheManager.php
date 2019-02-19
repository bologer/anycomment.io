<?php

namespace AnyComment\Cache;

use AnyComment\AnyCommentCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentRestCacheManager is used to manage cache related to REST API.
 */
class AnyCommentRestCacheManager extends AnyCommentCacheManager {

	/**
	 * @var string REST API cache namespace.
	 */
	public static $namespace = '/rest';

	/**
	 * @var string Post part of comments.
	 */
	public static $post = '/post/%s';

	/**
	 * @var string Post + count part.
	 */
	public static $post_count = '/post/count/%s';

	/**
	 * @var string Comments part of namespace.
	 */
	public static $comments = '/comments/%s';

	/**
	 * Get all comments of post.
	 *
	 * @param $post
	 *
	 * @return bool|\Stash\Interfaces\ItemInterface
	 */
	public static function getPostComments( $post ) {
		$postId = static::retrievePostId( $post );

		if ( $postId === null ) {
			return false;
		}


		return AnyCommentCore::cache()->getItem( static::buildPostNamespace( $postId ) );
	}

	/**
	 * Get post comment count.
	 *
	 * @param $post
	 *
	 * @return bool|\Stash\Interfaces\ItemInterface
	 */
	public static function getPostCommentCount( $post ) {
		$postId = static::retrievePostId( $post );

		if ( $postId === null ) {
			return false;
		}

		return AnyCommentCore::cache()->getItem( static::buildPostCommentCountNamespace( $postId ) );
	}

	/**
	 * Get single comment from specific post.
	 *
	 * @param $post
	 * @param $comment
	 *
	 * @return bool|\Stash\Interfaces\ItemInterface
	 */
	public static function getComment( $post, $comment ) {
		$postId    = static::retrievePostId( $post );
		$commentId = static::retrieveCommentId( $comment );

		if ( $postId === null || $commentId === null ) {
			return false;
		}

		return AnyCommentCore::cache()->getItem( static::buildCommentNamespace( $postId, $commentId ) );
	}

	/**
	 * Flush all REST API cache.
	 */
	public static function flush() {
		return AnyCommentCore::cache()->deleteItem( static::getNamespace() );
	}

	/**
	 * Flush comment count for specified post.
	 *
	 * @param int|\WP_Post $post Post ID to flush comments for.
	 *
	 * @return bool
	 */
	public static function flushPostCommentCount( $post ) {

		$postId = static::retrievePostId( $post );

		if ( $postId === null ) {
			return false;
		}

		return AnyCommentCore::cache()->deleteItem( static::buildPostCommentCountNamespace( $postId ) );
	}

	/**
	 * Flush all comments from certain post.
	 *
	 * @param int|\WP_Post $post Post ID to flush comments for.
	 *
	 * @return bool
	 */
	public static function flushPost( $post ) {

		$postId = static::retrievePostId( $post );

		if ( $postId === null ) {
			return false;
		}

		return AnyCommentCore::cache()->deleteItem( static::buildPostNamespace( $postId ) );
	}

	/**
	 * Flush single comment cache.
	 *
	 * @param int|\WP_Post $post Post object or ID.
	 * @param int|\WP_Comment $comment Comment object or ID.
	 *
	 * @return bool
	 */
	public static function flushComment( $post, $comment ) {

		$postId    = static::retrievePostId( $post );
		$commentId = static::retrieveCommentId( $comment );

		if ( $postId === null || $commentId === null ) {
			return false;
		}

		do {
			$tmpComment = get_comment( $commentId );

			$commentParent = (int) $tmpComment->comment_parent;

			if ( $commentParent !== 0 ) {
				AnyCommentCore::cache()->deleteItem( static::buildCommentNamespace( $tmpComment->comment_post_ID, $commentParent ) );
			}

			AnyCommentCore::cache()->deleteItem( static::buildCommentNamespace( $tmpComment->comment_post_ID, $commentId ) );

			$commentId = $tmpComment->comment_parent;

		} while ( $commentParent !== 0 );

		return true;
	}

	/**
	 * Build full namespace for single post.
	 *
	 * Example namespace returned:
	 * /anycomment/rest/post/{postId}
	 *
	 * @param int $postId Post ID.
	 *
	 * @return string
	 */
	public static function buildPostNamespace( $postId ) {
		return static::getNamespace() . sprintf( static::$post, $postId );
	}

	/**
	 * Build full namespace for single post comment count.
	 *
	 * Example namespace returned:
	 * /anycomment/rest/post/{postId}
	 *
	 * @param int $postId Post ID.
	 *
	 * @return string
	 */
	public static function buildPostCommentCountNamespace( $postId ) {
		return static::getNamespace() . sprintf( static::$post_count, $postId );
	}

	/**
	 * Build full namespace for single comment.
	 *
	 * Example namespace returned:
	 * /anycomment/rest/post/{postId}/comments/{commentId}
	 *
	 * @param int $postId Post ID.
	 * @param int $commentId Comment ID.
	 *
	 * @return string
	 */
	public static function buildCommentNamespace( $postId, $commentId ) {
		return static::getNamespace() . sprintf( static::$post, $postId ) . sprintf( static::$comments, $commentId );
	}

	/**
	 * Get base namespace.
	 *
	 * @return string
	 */
	public static function getNamespace() {
		return static::getRootNamespace() . static::$namespace;
	}

	/**
	 * Get post ID from passed comment param.
	 *
	 * @param int|\WP_Post $post Post to get ID from.
	 *
	 * @return int|null
	 */
	private static function retrievePostId( $post ) {
		if ( empty( $post ) ) {
			return null;
		}

		$postId = 0;

		if ( is_numeric( $post ) ) {
			$postId = $post;
		} elseif ( $post instanceof \WP_Post ) {
			$postId = $post->ID;
		}

		if ( (int) $postId === 0 ) {
			return null;
		}

		return (int) $postId;
	}

	/**
	 * Get comment ID from passed comment param.
	 *
	 * @param int|\WP_Comment $comment Comment to get ID from.
	 *
	 * @return int|null
	 */
	private static function retrieveCommentId( $comment ) {
		if ( empty( $comment ) ) {
			return null;
		}

		$commentId = 0;

		if ( is_numeric( $comment ) ) {
			$commentId = $comment;
		} elseif ( $comment instanceof \WP_Comment ) {
			$commentId = $comment->comment_ID;
		}

		if ( (int) $commentId === 0 ) {
			return null;
		}

		return (int) $commentId;
	}
}
