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
}