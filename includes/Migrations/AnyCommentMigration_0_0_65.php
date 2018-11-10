<?php

namespace AnyComment\Migrations;

use AnyComment\Models\AnyCommentEmailQueue;

class AnyCommentMigration_0_0_65 extends AnyCommentMigration {
	public $version = '0.0.65';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;

		$email_queue_table = AnyCommentEmailQueue::get_table_name();

		$check_email_queue = "SELECT *
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = '$email_queue_table'
    AND COLUMN_NAME = 'ID'
    AND DATA_TYPE = 'bigint'
    AND COLUMN_DEFAULT IS NULL
    AND IS_NULLABLE = 'NO'
    AND EXTRA like '%auto_increment%'";

		$res = $wpdb->get_results( $check_email_queue );

		return ! empty( $res );
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;
		/**
		 * Create email queue table
		 */
		$rating_table      = $wpdb->prefix . 'anycomment_rating';
		$email_queue_table = $wpdb->prefix . 'anycomment_email_queue';
		$sql_rating        = "ALTER TABLE $rating_table MODIFY COLUMN ID BIGINT AUTO_INCREMENT";
		$sql_email_queue   = "ALTER TABLE $email_queue_table MODIFY COLUMN ID BIGINT AUTO_INCREMENT";

		// Clean email queue table as it does not have AUTO INCREMENT
		$wpdb->query( "DELETE FROM $email_queue_table" );

		return ( false !== $wpdb->query( $sql_rating ) ) &&
		       ( false !== $wpdb->query( $sql_email_queue ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}
}

// eof;
