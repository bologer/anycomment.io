<?php

namespace AnyComment\Cache;


use AnyComment\AnyCommentCore;

/**
 * Class UserCache helps to manage user-related cache.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Cache
 */
class UserCache extends AnyCommentCacheManager {

	/**
	 * @var string Root caching namespace.
	 */
	public static $namespace = '/users';

	/**
	 * @var string Avatars template.
	 */
	public static $avatars = '/avatar_url/%s';


	/**
	 * Get avatar caching object.
	 *
	 * @param mixed $id_or_email Could be id, email or an object. In any wat it would be serialized.
	 *
	 * @return \Stash\Interfaces\ItemInterface
	 */
	public static function getAvatar( $id_or_email ) {
		$id_or_email = md5( serialize( $id_or_email ) );

		$cache_component = AnyCommentCore::cache();
		if ( $cache_component === null ) {
			return null;
		}

		return $cache_component->getItem( static::getUserNamespace() . sprintf( self::$avatars, $id_or_email ) );
	}

	public static function flushAvatars() {

	}

	/**
	 * Get user namespace.
	 *
	 * @return string
	 */
	public static function getUserNamespace() {
		return static::getRootNamespace() . static::$namespace;
	}

	/**
	 * Flush all user-related cache.
	 *
	 * @return bool
	 */
	public static function flushAll() {
		return AnyCommentCore::cache()->deleteItem( static::getRootNamespace() . self::$namespace );
	}
}