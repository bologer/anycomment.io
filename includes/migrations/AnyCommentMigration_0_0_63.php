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
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		/**
		 * Create email queue table
		 */
		$sql = "CREATE TABLE IF NOT EXISTS `{$this->getTable()}` (
				  `ID` bigint(20) UNSIGNED NOT NULL,
				  `post_ID` bigint(20) UNSIGNED NOT NULL,
				  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
				  `rating` smallint(1) SIGNED DEFAULT 5,
				  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
		return true;
	}
}

// eof;
