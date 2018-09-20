<?php

/**
 * Class AnyCommentCacheManager is used as base class for managing cache of the plugin.
 */
class AnyCommentCacheManager {

	/**
	 * @var string Root namespace. Can be used to flush cache globally.
	 */
	public static $rootNamespace = '/anycomment';

	/**
	 * Get available namespaces.
	 *
	 * @return array
	 */
	public static function getCacheNamespaces() {
		return [
			'rest' => 'AnyCommentRestCacheManager',
		];
	}

	/**
	 * Get root namespace.
	 *
	 * @return string
	 */
	public static function getRootNamespace() {
		return static::$rootNamespace;
	}
}