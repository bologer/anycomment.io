<?php

namespace AnyComment\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentManipulator is used
 */
class AnyCommentManipulatorHelper {

	/**
	 * Open comment for posts and pages.
	 *
	 * @since 0.0.59
	 *
	 * @param string|null $post_type Open comment for specific post type. E.g. post, page, etc.
	 *
	 * @return bool
	 */
	public static function open_all_comments( $post_type = null ) {
		global $wpdb;

		$sql = "UPDATE {$wpdb->posts} SET `comment_status`='open' WHERE `comment_status` != 'open'";

		if ( $post_type !== null ) {
			$sql .= $wpdb->prepare( " AND `post_type`=%s", [ $post_type ] );
		}

		return $wpdb->query( $sql ) !== false;
	}

	/**
	 * Open all post comments (comes by default from WordPress).
	 *
	 * @since 0.0.59
	 * @return bool
	 */
	public static function open_all_post_comments() {
		return static::open_all_comments( 'post' );
	}

	/**
	 * Open all page comments (comes by default from WordPress).
	 *
	 * @since 0.0.59
	 * @return bool
	 */
	public static function open_all_page_comments() {
		return static::open_all_comments( 'page' );
	}

	/**
	 * Open all product comments (WooCommerce post type).
	 *
	 * @since 0.0.59
	 * @return bool
	 */
	public static function open_all_product_comments() {
		return static::open_all_comments( 'product' );
	}
}
