<?php

namespace AnyComment\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentLinkHelper is a link helper class.
 *
 * Consists of commonly used helpers for generating links.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Helpers
 */
class AnyCommentLinkHelper {

	/**
	 * Get Vkontakte group URI.
	 *
	 * @return string
	 */
	public static function get_vkontakte() {
		return 'https://vk.com/anycomment';
	}

	/**
	 * Get Telegram channel URI.
	 *
	 * @return string
	 */
	public static function get_telegram() {
		return 'https://t.me/' . static::get_telegram_slug();
	}

	/**
	 * Telegram group slug name.
	 *
	 * @param bool $pretty True if required to return camel case, false would return lowercase.
	 *
	 * @return string
	 */
	public static function get_telegram_slug( $pretty = false ) {
		if ( $pretty ) {
			return 'AnyComment';
		}

		return 'anycomment';
	}

	/**
	 * Get GitHub repository URL.
	 *
	 * @return string
	 */
	public static function get_github() {
		return 'https://github.com/bologer/anycomment.io';
	}

	/**
	 * Get all guides link.
	 *
	 * @return string
	 */
	public static function get_guides() {
		return static::format_url( "category/tutorials" );
	}

	/**
	 * Get link to the demo page.
	 *
	 * @return string
	 */
	public static function get_demo() {
		return static::format_url( 'demo' );
	}

	/**
	 * Get social guides link.
	 *
	 * @return string
	 */
	public static function get_social_guides() {
		return static::format_url( "category/tutorials/socials" );
	}

	/**
	 * Format URL with proper language.
	 *
	 * @param string $url URL to be formatted.
	 *
	 * @return string
	 */
	public static function format_url( $url ) {
		$locale = static::get_language();

		if ( $locale === 'ru' ) {
			$locale = '';
		} else {
			$locale .= '/';
		}

		return static::get_official_website() . $locale . $url;
	}

	/**
	 * Get official website URL.
	 *
	 * @return string
	 */
	public static function get_official_website() {
		return "https://plugin.anycomment.io/";
	}

    /**
     * Get SaaS URL.
     *
     * @return string
     */
	public static function get_service_website() {
	    return 'https://anycomment.io';
    }

	/**
	 * Get short version of the language.
	 *
	 * @return bool|string
	 */
	public static function get_language() {
		return substr( get_locale(), 0, 2 );
	}
}
