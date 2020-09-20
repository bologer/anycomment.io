<?php

namespace AnyComment\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WC_Product;
use AnyComment\Base\BaseObject;
use AnyComment\Admin\AnyCommentIntegrationSettings;

/**
 * Class AnyCommentWooCommerce is used as integration for WooCommerce.
 */
class AnyCommentWooCommerce extends BaseObject {
	/**
	 * @inheritDoc
	 */
	public function init() {
		if ( AnyCommentIntegrationSettings::is_replace_woocommerce_review_form() ) {
			add_filter( 'woocommerce_product_tabs', [ $this, 'woo_new_product_tab' ], 999 );
		}
	}

	/**
	 * Add new review tab to display comments.
	 *
	 * @param array $tabs List of WooCommerce tabs.
	 *
	 * @return array List of tabs.
	 */
	public function woo_new_product_tab( $tabs ) {
		/**
		 * @var $product WC_Product
		 */
		global $product;
		global $wpdb;
		$review_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1' AND comment_post_ID = %d AND comment_type IN ('review', 'comment', '')",
				$product->id
			)
		);

		$tabs['reviews'] = array(
			'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $review_count ),
			'priority' => 30,
			'callback' => function () {
				echo do_shortcode( '[anycomment include="true"]' );
			}
		);

		return $tabs;
	}
}
