<?php

class AnyCommentLinkHelper {

	/**
	 * Get Vkontakte group URI.
	 *
	 * @return string
	 */
	public static function getVkontakte() {
		return 'https://vk.com/anycomment';
	}

	/**
	 * Get Telegram channel URI.
	 *
	 * @return string
	 */
	public static function getTelegram() {
		return 'https://t.me/joinchat/BEUJEQ9aMmQsFX65MNRCDg';
	}

	/**
	 * Get GitHub repository URL.
	 *
	 * @return string
	 */
	public static function getGitHub() {
		return 'https://github.com/bologer/anycomment.io';
	}

	/**
	 * Get all guides link.
	 *
	 * @return string
	 */
	public static function getGuidesLink() {
		return static::formatUrl( "category/tutorials" );
	}

	/**
	 * Get social guides link.
	 *
	 * @return string
	 */
	public static function getSocialGuidesLink() {
		return static::formatUrl( "category/tutorials/socials" );
	}

	/**
	 * Format URL with proper language.
	 *
	 * @param string $url URL to be formatted.
	 *
	 * @return string
	 */
	public static function formatUrl( $url ) {
		$locale = static::getLanguage();

		if ( $locale === 'ru' ) {
			$locale = '';
		} else {
			$locale .= '/';
		}

		return static::getOfficialWebsite() . $locale . $url;
	}

	/**
	 * Get official website URL.
	 *
	 * @return string
	 */
	public static function getOfficialWebsite() {
		return "https://anycomment.io/";
	}

	/**
	 * Get short version of the language.
	 *
	 * @return bool|string
	 */
	public static function getLanguage() {
		return substr( get_locale(), 0, 2 );
	}
}