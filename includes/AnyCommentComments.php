<?php

namespace AnyComment;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use WP_Comment;
use AnyComment\Helpers\AnyCommentStringHelper;
use AnyComment\Admin\AnyCommentGenericSettings;

class AnyCommentComments
{
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
    public static function filter_moderate_words($text)
    {

        $filter_matches = [
            'match_count' => 0,
            'matches' => [],
            'filters' => [],
            'filtered_text' => $text
        ];

        if (empty($text)) {
            return $filter_matches;
        }

        $moderate_words = trim(AnyCommentGenericSettings::get_moderate_words());

        if (empty($moderate_words)) {
            return $filter_matches;
        }

        $words = explode(",", $moderate_words);
        $words = array_map('trim', $words);

//		$replace_regex = '/(.*):([^\w]{1})/m';

        foreach ($words as $word) {
            // Skip empty lines.
            if (empty($word)) {
                continue;
            }

            $exploded = explode(':', $word);

            $replace_character = null;

            if (count($exploded) === 2 && !empty($exploded[1])) {
                $word = trim($exploded[0]);
                $replace_character = trim($exploded[1]);
            }

            $pattern = "/$word/miu";

            if (preg_match($pattern, $text, $matches)) {
                if (isset($matches[0])) {
                    $match = trim($matches[0]);
                    $match_char_length = AnyCommentStringHelper::mb_strlen($match);
                    $filter_matches['match_count']++;
                    $filter_matches['matches'][] = $word;
                    if ($replace_character !== null) {
                        $filter_matches['filtered_text'] = str_replace($match, str_repeat($replace_character, $match_char_length), $filter_matches['filtered_text']);
                    }
                }
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
    public static function has_links($comment)
    {
        $comment = get_comment($comment);

        if (!$comment) {
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
        foreach ($link_matcher_array as $link_matcher) {
            if (strpos($comment_text, $link_matcher) !== false) {
                $hasCount++;
            }
        }

        return $hasCount > 0;
    }
}
