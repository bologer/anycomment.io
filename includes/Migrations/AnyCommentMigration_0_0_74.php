<?php

namespace AnyComment\Migrations;

/**
 * Class AnyCommentMigration_0_0_74 is used to add type column to `anycomment_likes` table
 * to control whether it is like or dislike.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Migrations
 */
class AnyCommentMigration_0_0_74 extends AnyCommentMigration {
	public $version = '0.0.74';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;

		$table = $wpdb->prefix . 'anycomment_likes';

		$res = $wpdb->get_results( "SHOW COLUMNS FROM $table LIKE 'type';", 'ARRAY_A' );

		return ! empty( $res ) && count( $res );
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$table = $wpdb->prefix . 'anycomment_likes';

		$sql = "ALTER TABLE $table ADD COLUMN `type` TINYINT(1) DEFAULT 1";

		return false !== $wpdb->query( $sql );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$table = $wpdb->prefix . 'anycomment_likes';

		$sql = "ALTER TABLE $table DROP COLUMN `type`";

		return false !== $wpdb->query( $sql );
	}
}

// eof;
