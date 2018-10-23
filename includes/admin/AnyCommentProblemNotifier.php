<?php

class AnyCommentProblemNotifier {
	const TYPE_PLUGIN = 'plugin';
	const TYPE_GENERIC = 'generic';

	const LEVEL_CRITICAL = 'critical';
	const LEVEL_LOW = 'low';

	/**
	 * Get list of problematic issue analyzed from website.
	 * @return array
	 */
	public static function get_problem_list() {
		$items = [];

		global $wp_version;

		if ( version_compare( $wp_version, '3.7', '<' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_GENERIC,
				'message' => sprintf( __( 'WordPress version you are using is lower then 3.7. AnyComment rely on REST API. WordPress introduced it in 3.7.', 'anycomment' ) )
			];
		}

		if ( is_plugin_active( 'clearfy/clearfy.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( 'You have <a href="%s">Clearfy</a> activated, please make sure "Remove REST API Links" is "Off" under "Performance" tab as it may cause problems to load comments.', 'anycomment' ), '/wp-admin/admin.php?page=performance-wbcr_clearfy' )
			];
		}

		if ( is_plugin_active( 'all-in-one-wp-security-and-firewall/wp-security.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( 'You have <a href="%s">All In One WP Security</a> activated, please make sure "Disallow Unauthorized REST Requests" is unchecked under "WP REST API" tab as it may cause problems to load comments.', 'anycomment' ), '/wp-admin/admin.php?page=aiowpsec_misc&tab=tab4' )
			];
		}

		if ( is_plugin_active( 'wpdiscuz/class.WpdiscuzCore.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for comments. This is same type of plugin as AnyComment, please deactivate it as it may cause problems.", "anycomment" ), 'wpDiscuz' )
			];
		}

		if ( is_plugin_active( 'jetpack/jetpack.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for comments. This is same type of plugin as AnyComment, please deactivate it as it may cause problems.", "anycomment" ), 'Jetpack' )
			];
		}

		if ( is_plugin_active( 'disqus-comment-system/disqus.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for comments. This is same type of plugin as AnyComment, please deactivate it as it may cause problems.", "anycomment" ), 'Disqus Comment System ' )
			];
		}

		if ( is_plugin_active( 'disable-comments/disable-comments.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s. This plugin is used to disable comments, please deactivate it as it may cause problems.", "anycomment" ), 'Disable Comments' )
			];
		}

		if ( is_plugin_active( 'psn-pagespeed-ninja/pagespeedninja.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'PageSpeed Ninja' )
			];
		}

		if ( is_plugin_active( 'swift-performance-lite/performance.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Swift Performance Lite' )
			];
		}

		if ( is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'WP Fastest Cache' )
			];
		}

		if ( is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'LiteSpeed Cache' )
			];
		}

		if ( is_plugin_active( 'wp-super-cache/wp-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'WP Super Cache' )
			];
		}

		if ( is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'W3 Total Cache' )
			];
		}

		if ( is_plugin_active( 'comet-cache/comet-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Comet Cache' )
			];
		}

		if ( is_plugin_active( 'autoptimize/autoptimize.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Autoptimize' )
			];
		}

		if ( is_plugin_active( 'redis-cache/redis-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Redis Object Cache' )
			];
		}


		return $items;
	}
}