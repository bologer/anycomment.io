<?php

class AnyCommentMigration_0_0_63 extends AnyCommentMigration {
	public $table   = 'rating';
	public $version = '0.0.63';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;
		$res = $wpdb->get_results( "SHOW TABLES LIKE '{$this->getTable()}';", 'ARRAY_A' );

		return ! empty( $res ) && count( $res ) == 1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		update_option( 'anycomment_migration', '0.0.61' );
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}
}

// eof;
