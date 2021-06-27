<?php

namespace AnyComment\Repository;

class UserRepository {
	public function findByIds( array $ids ) {
		global $wpdb;
		foreach ( $ids as $id ) {
			if ( ! is_int( $id ) ) {
				throw new \InvalidArgumentException( 'Only numeric values allowed.' );
			}
		}
		$ids = implode( ',', $ids );

		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $wpdb->users WHERE ID IN ($ids)" ),
			ARRAY_A
		);
	}
}
