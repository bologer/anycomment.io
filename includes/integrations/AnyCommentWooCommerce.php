<?php

/**
 * Class AnyCommentWooCommerce is used as integration for WooCommerce.
 */
class AnyCommentWooCommerce {

	/**
	 * AnyCommentWooCommerce constructor.
	 */
	public function __construct() {
		$this->init();
	}

	public function init() {
		add_filter( 'woocommerce_product_tabs', [ $this, 'woo_new_product_tab' ], 98 );
	}


	/**
	 * Add new review tab to display comments.
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function woo_new_product_tab( $tabs ) {
		unset( $tabs['reviews'] );

		$tabs['anycomment_reviews'] = array(
			'title'    => __( 'Reviews', 'anycomment' ),
			'priority' => 50,
			'callback' => function () {
				echo do_shortcode( '[anycomment include="true"]' );
			}
		);

		return $tabs;
	}
}