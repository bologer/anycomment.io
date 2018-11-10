<?php

namespace AnyComment\Helpers;

/**
 * Class AnyCommentInflector is used to have string manipulation methods.
 *
 * Credits to BaseInflector from Yii2. Link below.
 * @link https://github.com/yiisoft/yii2/blob/master/framework/helpers/BaseInflector.php
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Helpers
 * @since 0.0.70
 */
class AnyCommentInflector {
	/**
	 * Shortcut for `Any-Latin; Latin-ASCII; [\u0080-\uffff] remove` transliteration rule.
	 *
	 * The rule is loose,
	 * letters will be transliterated with the characters of Basic Latin Unicode Block.
	 * For example:
	 * `获取到 どちら Українська: ґ,є, Српска: ђ, њ, џ! ¿Español?` will be transliterated to
	 * `huo qu dao dochira Ukrainska: g,e, Srpska: d, n, d! Espanol?`.
	 *
	 * Used in [[transliterate()]].
	 * For detailed information see [unicode normalization forms](http://unicode.org/reports/tr15/#Normalization_Forms_Table)
	 * @see http://unicode.org/reports/tr15/#Normalization_Forms_Table
	 * @see transliterate()
	 * @since 2.0.7
	 */
	const TRANSLITERATE_LOOSE = 'Any-Latin; Latin-ASCII; [\u0080-\uffff] remove';

	/**
	 * @var mixed Either a [[\Transliterator]], or a string from which a [[\Transliterator]] can be built
	 * for transliteration. Used by [[transliterate()]] when intl is available. Defaults to [[TRANSLITERATE_LOOSE]]
	 * @see http://php.net/manual/en/transliterator.transliterate.php
	 */
	public static $transliterator = self::TRANSLITERATE_LOOSE;

	/**
	 * Returns transliterated version of a string.
	 *
	 * If intl extension isn't available uses fallback that converts latin characters only
	 * and removes the rest. You may customize characters map via file required in get_dictionary() method.
	 *
	 * @see get_dictionary() to find path to the file which consists map of replacements.
	 *
	 * @param string $string input string
	 * @param string|\Transliterator $transliterator either a [[\Transliterator]] or a string
	 * from which a [[\Transliterator]] can be built.
	 *
	 * @return string
	 * @since 0.0.70
	 */
	public static function transliterate( $string, $transliterator = null ) {
		$hasIntl = extension_loaded( 'intl' );

		if ( $hasIntl ) {
			if ( $transliterator === null ) {
				$transliterator = static::$transliterator;
			}

			return transliterator_transliterate( $transliterator, $string );
		}

		$string = strtr( $string, static::get_dictionary() );

		if ( function_exists( 'iconv' ) ) {
			// Remove other junky chars
			$string = iconv( 'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $string );
		}

		return $string;
	}

	/**
	 * Transliterate dictionary. Returns map of characters
	 * and its replacements. See required file for further
	 * information.
	 *
	 * @return array
	 */
	public static function get_dictionary() {
		return require __DIR__ . '/data/transliterate.php';
	}
}