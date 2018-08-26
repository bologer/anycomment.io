<?php

class AnyCommentMigration_0_0_50 extends AnyCommentMigration {
	public $table = 'email_queue';
	public $version = '0.0.50';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {

		global $wpdb;
		$res = $wpdb->get_results( "SHOW COLUMNS FROM `{$this->getTable()}` LIKE 'subject';", 'ARRAY_A' );

		return $res !== null && count( $res ) > 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$table = $this->getTable();

		/**
		 * Create email queue table
		 */
		$sql = "ALTER TABLE `$table` ADD `subject` VARCHAR(255) NOT NULL";

		return $wpdb->query( $sql ) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;
		$sql = sprintf( "ALTER COLUMN `%s` DROP COLUMN `%s`;", $this->getTable(), 'subject' );


		return $wpdb->query( $sql );
	}
}