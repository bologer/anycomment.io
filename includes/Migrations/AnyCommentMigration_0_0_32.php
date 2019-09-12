<?php

namespace AnyComment\Migrations;

class AnyCommentMigration_0_0_32 extends AnyCommentMigration {
	public $version = '0.0.32';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		global $wpdb;

		$res = $wpdb->get_results( "SHOW TABLES LIKE 'anycomment_likes';", 'ARRAY_A' );

		if ( ! empty( $res ) && count( $res ) == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$table           = 'anycomment_likes';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table` (
			  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `user_ID` bigint(20) UNSIGNED NULL,
			  `comment_ID` bigint(20) UNSIGNED NOT NULL,
			  `post_ID` bigint(20) UNSIGNED NOT NULL,
			  `type` TINYINT(1) DEFAULT 1,
			  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `liked_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`ID`),
			  KEY `user_ID` (`user_ID`),
			  KEY `post_ID` (`post_ID`)
			) $charset_collate;";

		$wpdb->query( $sql );

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$sql = 'DROP TABLE IF EXISTS `anycomment_likes`;';
		$wpdb->query( $sql );

		return true;
	}
}

// eof;
