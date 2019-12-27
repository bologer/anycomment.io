<?php

namespace AnyComment\Hooks;

use AnyComment\Base\BaseObject;
use AnyComment\Cache\UserCache;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentUserHooks is used to add hooks related to the user.
 *
 * For example, it can hook actions such as after user logged in, etc.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Hooks
 */
class AnyCommentUserHooks extends BaseObject {

	/**
	 * @inheritDoc
	 */
	public function init() {
		add_action( 'anycomment/user/logged_in', [ $this, 'drop_cache_on_login' ], 11, 2 );
		add_action( 'anycomment/admin/options/update', [ $this, 'drop_cache_on_options_update' ], 11, 2 );
	}

	/**
	 * Drop cache related to options update.
	 *
	 * @param string $option Single setting.
	 * @param array $options List of updated settings.
	 */
	public function drop_cache_on_options_update ( $option, $options ) {
		// Flush all cache related to users
		UserCache::flushAll();
	}

	/**
	 * Drop user login cache, to get rid of incorrect cookie nonce issue.
	 *
	 * @param \WP_User $user Instance of WordPress user.
	 * @param string $post_url Post URL.
	 *
	 * @return bool False on failure to get post ID by provided URL.
	 */
	public function drop_cache_on_login ( $user, $post_url ) {

		$post_id = url_to_postid( $post_url );

		if ( 0 === $post_id ) {
			return false;
		}

		/**
		 * WP Super Cache fix.
		 * @link https://wordpress.org/plugins/wp-super-cache/
		 */
		if ( function_exists( 'wpsc_delete_post_cache' ) ) {
			wpsc_delete_post_cache( $post_id );
		}

		/**
		 * WP Rocket fix.
		 * @link https://wp-rocket.me/
		 */
		if ( function_exists( 'rocket_clean_post' ) ) {
			rocket_clean_post( $post_id );
		}

		/**
		 * WP Fastest Cache fix.
		 * @link https://wordpress.org/plugins/wp-fastest-cache/
		 */
		if ( class_exists( '\WpFastestCache' ) && method_exists( '\WpFastestCache', 'singleDeleteCache' ) ) {
			( new \WpFastestCache() )->singleDeleteCache( false, $post_id );
		}

		/**
		 * W3 Total Cache fix.
		 * @link https://wordpress.org/plugins/w3-total-cache/
		 */
		if ( function_exists( 'w3tc_flush_post' ) ) {
			w3tc_flush_post( $post_id );
		}

		return true;
	}
}
