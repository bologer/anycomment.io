<?php

class AnyCommentMigration_0_0_50 extends AnyCommentMigration {
	public $table = 'email_queue';
	public $version = '0.0.50';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;


		$res  = $wpdb->get_results( "SHOW COLUMNS FROM `{$this->getTable()}` LIKE 'subject';", 'ARRAY_A' );
		$res2 = $wpdb->get_results( "SHOW COLUMNS FROM `{$this->getTable()}` LIKE 'email';", 'ARRAY_A' );

		return $res !== null && count( $res ) > 0 &&
		       $res2 !== null && count( $res2 ) > 0;
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
		$arr   = [];
		$arr[] = "ALTER TABLE `$table` ADD COLUMN `subject` VARCHAR(255) NOT NULL";
		$arr[] = "ALTER TABLE `$table` ADD COLUMN `email` VARCHAR(255) DEFAULT NULL";
		$arr[] = "ALTER TABLE `$table` ADD COLUMN `is_sent` BOOL DEFAULT 0";
		$arr[] = "ALTER TABLE `$table` DROP COLUMN `sent_at`;";
		$arr[] = "ALTER TABLE `$table` DROP COLUMN `user_ID`;";


		$count = 0;
		foreach ( $arr as $query ) {
			if ( $wpdb->query( $query ) !== false ) {
				$count ++;
			}
		}

		$wpdb->update( $table, [ 'is_sent' => 1 ], [ 'is_sent' => 0 ] );

		return count( $arr ) === $count;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$table = $this->getTable();

		$arr   = [];
		$arr[] = sprintf( "ALTER TABLE `%s` DROP COLUMN `%s`;", $table, 'subject' );
		$arr[] = sprintf( "ALTER TABLE `%s` DROP COLUMN `%s`;", $table, 'email' );
		$arr[] = sprintf( "ALTER TABLE `%s` DROP COLUMN `%s`;", $table, 'is_sent' );
		$arr[] = sprintf( "ALTER TABLE `%s` ADD COLUMN `%s` datetime DEFAULT '0000-00-00 00:00:00';", $table, 'sent_at' );
		$arr[] = sprintf( "ALTER TABLE `%s` ADD COLUMN `%s` bigint(20) NOT NULL;", $table, 'user_ID' );

		$count = 0;
		foreach ( $arr as $query ) {
			if ( $wpdb->query( $query ) !== false ) {
				$count ++;
			}
		}

		return count( $arr ) === $count;
	}
}