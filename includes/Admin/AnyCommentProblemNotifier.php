<?php

namespace AnyComment\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentProblemNotifier checks active plugins, scripts, etc
 * for possible conflicts with AnyComment.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Admin
 */
class AnyCommentProblemNotifier {
	const TYPE_PLUGIN = 'plugin';
	const TYPE_GENERIC = 'generic';

	const LEVEL_CRITICAL = 'critical';
	const LEVEL_LOW = 'low';

	/**
	 * Get list of problematic issue analyzed from website.
	 * @return array
	 */
	public static function get_problem_list () {
		$items = [];

		global $wp_version;

		if ( version_compare( $wp_version, '3.7', '<' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_GENERIC,
				'message' => __( 'WordPress version you are using is lower then 3.7. AnyComment rely on REST API. WordPress introduced it in 3.7.', 'anycomment' ),
			];
		}

		if ( is_plugin_active( 'better-wp-security/better-wp-security.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => __( 'You have iThemes Security activated, please make sure "Filter Long URL Strings" is "Off" as it may cause problems.', 'anycomment' ),
			];
		}

		if ( is_plugin_active( 'clearfy/clearfy.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => __( 'You have Clearfy activated, please make sure "Remove REST API Links" is "Off" under "Performance" tab and all options under "Server headers and response" in "SEO" tab are "Off" as it may cause problems to load comments.', 'anycomment' ),
			];
		}

		if ( is_plugin_active( 'all-in-one-wp-security-and-firewall/wp-security.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( 'You have All In One WP Security activated, please make sure "Disallow Unauthorized REST Requests" is unchecked in <a href="%s">here</a> and "Bad Query Strings" is unchecked in <a href="%s">here</a> tab as it may cause problems to load comments.', 'anycomment' ),
					'/wp-admin/admin.php?page=aiowpsec_misc&tab=tab4',
					'/wp-admin/admin.php?page=aiowpsec_firewall&tab=tab2' ),
			];
		}

		if ( is_plugin_active( 'wpdiscuz/class.WpdiscuzCore.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for comments. This is same type of plugin as AnyComment, please deactivate it as it may cause problems.", "anycomment" ), 'wpDiscuz' ),
			];
		}

		if ( is_plugin_active( 'jetpack/jetpack.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for comments. This is same type of plugin as AnyComment, please deactivate it as it may cause problems.", "anycomment" ), 'Jetpack' ),
			];
		}

		if ( is_plugin_active( 'disqus-comment-system/disqus.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for comments. This is same type of plugin as AnyComment, please deactivate it as it may cause problems.", "anycomment" ), 'Disqus Comment System ' ),
			];
		}

		if ( is_plugin_active( 'disable-comments/disable-comments.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s. This plugin is used to disable comments, please deactivate it as it may cause problems.", "anycomment" ), 'Disable Comments' ),
			];
		}

		if ( is_plugin_active( 'psn-pagespeed-ninja/pagespeedninja.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'PageSpeed Ninja' ),
			];
		}

		if ( is_plugin_active( 'swift-performance-lite/performance.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Swift Performance Lite' ),
			];
		}

		if ( is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'WP Fastest Cache' ),
			];
		}

		if ( is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'LiteSpeed Cache' ),
			];
		}

		if ( is_plugin_active( 'wp-super-cache/wp-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. Go to \"Settings\" and make sure option \"304 Not Modified...\" is unchecked. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'WP Super Cache' ),
			];
		}

		if ( is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'W3 Total Cache' ),
			];
		}

		if ( is_plugin_active( 'comet-cache/comet-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Comet Cache' ),
			];
		}

		if ( is_plugin_active( 'autoptimize/autoptimize.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Autoptimize' ),
			];
		}

		if ( is_plugin_active( 'redis-cache/redis-cache.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_LOW,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s for caching. If you're experiencing some problem with AnyComment, try to deactivate it and check whether problem was resolved", "anycomment" ), 'Redis Object Cache' ),
			];
		}

		if ( is_plugin_active( 'custom-post-type-ui/custom-post-type-ui.php' ) ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_PLUGIN,
				'message' => sprintf( __( "You are using %s. If you're experiencing some problem with AnyComment, try to set option 'Show in REST API' as 'true' in 'Add/Edit Taxonomies' or deactivate it and check whether problem was resolved", "anycomment" ), 'Custom Post Type UI' ),
			];
		}

		if ( static::has_functionphp_problem() ) {
			$items[] = [
				'level'   => self::LEVEL_CRITICAL,
				'type'    => self::TYPE_GENERIC,
				'message' => sprintf( __( "It looks like you have hooks related to REST API in %s which may cause failure to load comments. If you are not sure how to fix it, contact plugin developer in on the contacts available in 'Help' tab.", "anycomment" ), get_template_directory() . '/functions.php' ),
			];
		}


		return $items;
	}

	/**
	 * Checks whether function.php may contain some WP REST API hooks.
	 *
	 * @return bool
	 */
	public static function has_functionphp_problem () {
		$file_path = get_template_directory() . '/functions.php';

		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		$content = @file_get_contents( $file_path );

		if ( empty( $content ) ) {
			return false;
		}

		$possible_problems = [
			'rest_enabled',
			'rest_output_rsd',
			'rest_cookie_collect_status',
			'rest_cookie_check_errors',
			'rest_api_init',
			'rest_api_default_filters',
			'rest_api_loaded',
			'rest_api_init',
			'rest_pre_serve_request',
		];

		$count = 0;

		foreach ( $possible_problems as $problem ) {
			if ( strpos( $content, $problem ) !== false ) {
				$count ++;
			}
		}

		return $count > 0;
	}
}
