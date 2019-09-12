<?php

namespace AnyComment\Migrations;

class AnyCommentMigration_0_0_61 extends AnyCommentMigration {
	public $table = 'anycomment_rating';
	public $version = '0.0.61';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		global $wpdb;

		$table = $wpdb->prefix . $this->table;
		$res   = $wpdb->get_results( "SHOW TABLES LIKE '$table';", 'ARRAY_A' );

		return ! empty( $res ) && count( $res ) == 1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table = $table = $wpdb->prefix . $this->table;
		/**
		 * Create email queue table
		 */
		$sql = "CREATE TABLE `$table` (
				  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `post_ID` bigint(20) UNSIGNED NOT NULL,
				  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
				  `rating` smallint(1) SIGNED DEFAULT 5,
				  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `created_at` bigint(20) NOT NULL,
				  PRIMARY KEY (`ID`),
				  INDEX post_ID (`post_ID`),
				  INDEX user_ID (`user_ID`)
				) $charset_collate;";

		return $wpdb->query( $sql ) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;
		$sql   = "DROP TABLE IF EXISTS `$table`;";
		$wpdb->query( $sql );

		return true;
	}
}

// eof;
