<?php

namespace AnyComment\Migrations;

class AnyCommentMigration_0_0_68 extends AnyCommentMigration {
	public $table = 'anycomment_subscriptions';
	public $version = '0.0.68';

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

		$table = $wpdb->prefix . $this->table;
		$sql   = "CREATE TABLE IF NOT EXISTS `$table` (
				  `ID` bigint(20) UNSIGNED NOT NULL auto_increment,
				  `post_ID` bigint(20) UNSIGNED NOT NULL,
				  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
				  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `is_active` smallint(1) SIGNED DEFAULT 1,
				  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `confirmed_at`  bigint(20) NULL,
				  `created_at` bigint(20) NOT NULL,
				  PRIMARY KEY (`ID`),
				  KEY `post_ID` (`post_ID`),
				  KEY `user_ID` (`user_ID`)
				) $charset_collate;";

		return ( false !== $wpdb->query( $sql ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$table = $wpdb->prefix . $this->table;
		$sql   = "DROP TABLE IF EXISTS $table;";

		$wpdb->query( $sql );

		return true;
	}
}

// eof;
