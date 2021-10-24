<?php

namespace AnyComment\Admin\Tables;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class AnyCommentTable extends \WP_List_Table {

	protected function getOrderByParam( $defaultValue = null ) {
		$orderByValue = ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : $defaultValue;
		$this->assertOrderByColumn( $orderByValue );

		return $orderByValue;
	}

	protected function getOrderParam( $defaultValue = 'DESC' ) {
		if ( array_key_exists( 'order', $_GET ) ) {
			$orderValue = sanitize_text_field( $_GET['order'] );
			$orderValue = strtoupper( trim( $orderValue ) );
		} else {
			$orderValue = $defaultValue;
		}
		$this->assertOrder( $orderValue );

		return $orderValue;
	}

	protected function orderByColumns() {
		return [];
	}

	protected function assertOrderByColumn( $orderByColumn ) {
		if ( ! in_array( $orderByColumn, $this->orderByColumns() ) ) {
			throw new \Exception( 'Column ' . $orderByColumn . ' is not in the list of order columns' );
		}
	}

	protected function assertOrder( $order ) {
		$orderCapitalCase    = strtoupper( $order );
		$availableOrderTypes = [ 'ASC', 'DESC' ];
		if ( ! in_array( $orderCapitalCase, $availableOrderTypes ) ) {
			throw new \Exception(
				'Unknown order ' . $orderCapitalCase . ', available: ' . implode( ',', $availableOrderTypes )
			);
		}
	}
}
