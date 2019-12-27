<?php

namespace AnyComment\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WC_Product;
use AnyComment\Base\BaseObject;

/**
 * Class AnyCommentWooCommerce is used as integration for WooCommerce.
 */
class AnyCommentWooCommerce extends BaseObject {
    /**
	 * @inheritDoc
	 */
	public function init() {
		add_filter( 'woocommerce_product_tabs', [ $this, 'woo_new_product_tab' ], 999 );
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

		$tabs['reviews'] = array(
			'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $product->get_review_count() ),
			'priority' => 30,
			'callback' => function () {
				echo do_shortcode( '[anycomment include="true"]' );
			}
		);

		return $tabs;
	}
}
