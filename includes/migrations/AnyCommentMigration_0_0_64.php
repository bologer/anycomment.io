<?php

class AnyCommentMigration_0_0_64 extends AnyCommentMigration {
	public $table = 'rating';
	public $version = '0.0.64';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		return version_compare( get_option( 'anycomment_migration' ), $this->version, '>=' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;
		/**
		 * Create email queue table
		 */
		$sql = "ALTER TABLE {$this->getTable()} MODIFY COLUMN ID INT AUTO_INCREMENT";

		return ( false !== $wpdb->query( $sql ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}
}

// eof;
