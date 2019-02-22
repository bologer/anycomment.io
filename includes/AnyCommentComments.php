<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_Comment;
use AnyComment\Admin\AnyCommentGenericSettings;

class AnyCommentComments {

	/**
	 * Check for moderates list of words.
	 *
	 * Note 1: When no words specified in the list, method will no check for anything.
	 * Note 2: When moderation first disabled, method will no check for anything.
	 *
	 * @param string $text Comment text.
	 *
	 * @return array Information about processed text. See possible keys below:
	 * - match_count: number of items were matched by filter
	 * - matches: matched words
	 * - filters: key => value list, where key is what was filtered and value is to what it was changed
	 * - filtered_text: is final filtered text
	 */
	public static function filter_moderate_words( $text ) {

		$filter_matches = [
			'match_count'         => 0,
			'matches'       => [],
			'filters'       => [],
			'filtered_text' => $text
		];

		if ( empty( $text ) ) {
			return $filter_matches;
		}

		$moderate_words = trim( AnyCommentGenericSettings::get_moderate_words() );

		if ( empty( $moderate_words ) ) {
			return $filter_matches;
		}

		$words = explode( ",", $moderate_words );
		$words = array_map( 'trim', $words );

//		$replace_regex = '/(.*):([^\w]{1})/m';

		foreach ( $words as $word ) {
			// Skip empty lines.
			if ( empty( $word ) ) {
				continue;
			}

//			$original_word = $word;
			$word = preg_quote( $word, '#' );

//			if ( preg_match( $replace_regex, $original_word, $matches ) ) {
//
//				$matched_word = isset( $matches[1] ) ? preg_quote( trim( $matches[1] ), '#' ) : null;
//				$replace_char = isset( $matches[2] ) ? trim( $matches[2] ) : null;
//
//				if ( empty( $matched_word ) || empty( $replace_char ) ) {
//					continue;
//				}
//
//				if ( preg_match( "#$matched_word#i", $text ) ) {
//
//					$filter_matches['count']     = $filter_matches['count'] ++;
//					$filter_matches['matches'][] = $matched_word;
//
//					$rands  = [];
//					$length = strlen( utf8_decode( $matched_word ) );
//
//					do {
//						$rand_int = rand( 0, $length );
//
//						if ( ! isset( $rands[ $rand_int ] ) ) {
//							$rands[ $rand_int ] = $rand_int;
//						}
//
//					} while ( count( $rands ) < $length - 2 ); // -2 because should save at least one chart from word
//
//					$word_replaced = utf8_encode( $matched_word );
//					foreach ( $rands as $pos ) {
//						$word_replaced[ $pos ] = utf8_encode($replace_char);
//					}
//
//					$word_replaced = utf8_decode( $word_replaced );
//
//					$filter_matches['filters'][ $matched_word ] = $word_replaced;
//					$filter_matches['filtered_text']            = str_replace( $matched_word, $word_replaced, $text );
//				}
//			} else
			if ( preg_match( "#$word#i", $text ) ) {
				$filter_matches['match_count']     = $filter_matches['match_count'] ++;
				$filter_matches['matches'][] = $word;
			}
		}

		return $filter_matches;
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
