<?php

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
	 * @var string Comments part of namespace.
	 */
	public static $comments = '/comments/%s';

	/**
	 * Flush all REST API cache.
	 */
	public static function flush() {
		AnyComment()->cache->deleteItem( static::getNamespace() );
	}

	/**
	 * Flush all comments from certain post.
	 *
	 * @param int|WP_Post $post Post ID to flush comments for.
	 *
	 * @return bool
	 */
	public static function flushPost( $post ) {

		$commentId = static::retrievePostId( $post );

		if ( $commentId === null ) {
			return false;
		}

		AnyComment()->cache->deleteItem( static::buildPostNamespace( $post ) );

		return true;
	}

	/**
	 * Flush single comment cache.
	 *
	 * @param int|WP_Post $post Post object or ID.
	 * @param int|WP_Comment $comment Comment object or ID.
	 *
	 * @return bool
	 */
	public static function flushComment( $post, $comment ) {

		$postId    = static::retrievePostId( $post );
		$commentId = static::retrieveCommentId( $comment );

		if ( $postId === null || $commentId === null ) {
			return false;
		}

		AnyComment()->cache->deleteItem( static::buildCommentNamespace( $postId, $comment ) );

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
	 * @param int|WP_Post $post Post to get ID from.
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
		} elseif ( $post instanceof WP_Post ) {
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
	 * @param int|WP_Comment $comment Comment to get ID from.
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
		} elseif ( $comment instanceof WP_Comment ) {
			$commentId = $comment->comment_ID;
		}

		if ( (int) $commentId === 0 ) {
			return null;
		}

		return (int) $commentId;
	}
}