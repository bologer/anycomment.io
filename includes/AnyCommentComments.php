<?php

class AnyCommentComments {

	/**
	 * Check for moderates list of words.
	 *
	 * Note 1: When no words specified in the list, method will no check for anything.
	 * Note 2: When moderation first disabled, method will no check for anything.
	 *
	 * @param int|WP_Comment $comment
	 *
	 * @return bool
	 */
	public static function hasModerateWords( $comment ) {

		$comment = get_comment( $comment );

		if ( ! $comment ) {
			return false;
		}

		if ( ! AnyCommentGenericSettings::isModerateFirst() ) {
			return false;
		}

		$moderate_words = trim( AnyCommentGenericSettings::getModerateWords() );

		if ( empty( $moderate_words ) ) {
			return false;
		}

		$words = (array) explode( ",", $moderate_words );

		foreach ( $words as $word ) {
			$word = trim( $word );

			// Skip empty lines.
			if ( empty( $word ) ) {
				continue;
			}

			$word = preg_quote( $word, '#' );

			if ( preg_match( "#$word#i", $comment->comment_content ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether comment contains link(s) or not.
	 *
	 * @param mixed $comment Comment to be checked for links.
	 *
	 * @return bool
	 */
	public static function has_links( $comment ) {
		$comment = get_comment( $comment );

		if ( ! $comment ) {
			return false;
		}

		$comment_text = $comment->comment_content;

		$link_matcher_array = [
			'http',
			'https',
			'www.',
			'://'
		];

		$hasCount = 0;
		foreach ( $link_matcher_array as $link_matcher ) {
			if ( strpos( $comment_text, $link_matcher ) !== false ) {
				$hasCount ++;
			}
		}

		return $hasCount > 0;
	}
}