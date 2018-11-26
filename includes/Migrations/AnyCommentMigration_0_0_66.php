<?php

namespace AnyComment\Migrations;

class AnyCommentMigration_0_0_66 extends AnyCommentMigration {
	public $table = 'anycomment_email_queue';
	public $version = '0.0.66';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		global $wpdb;

		$table = $wpdb->prefix . $this->table;

		$subjectColumn = $wpdb->get_results( "SHOW COLUMNS FROM $table LIKE 'subject';", 'ARRAY_A' );
		$emailColumn   = $wpdb->get_results( "SHOW COLUMNS FROM $table LIKE 'email';", 'ARRAY_A' );
		$sentColumn    = $wpdb->get_results( "SHOW COLUMNS FROM $table LIKE 'is_sent';", 'ARRAY_A' );


		return ! empty( $subjectColumn ) && count( $subjectColumn ) > 0 &&
		       ! empty( $emailColumn ) && count( $emailColumn ) > 0 &&
		       ! empty( $sentColumn ) && count( $sentColumn ) > 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$table = $wpdb->prefix . $this->table;

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
		return true;
	}
}

// eof;
