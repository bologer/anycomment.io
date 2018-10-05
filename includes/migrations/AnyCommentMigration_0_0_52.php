<?php

class AnyCommentMigration_0_0_52 extends AnyCommentMigration {
	public $table = 'uploaded_files';
	public $version = '0.0.52';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;
		$res = $wpdb->get_results( "SHOW TABLES LIKE 'anycomment_uploaded_files';", 'ARRAY_A' );

		return count( $res ) > 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;
		
		/**
		 * Create email queue table
		 */
		$sql = "CREATE TABLE `anycomment_uploaded_files` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_ID` bigint(20) UNSIGNED NOT NULL,
  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` bigint(20) NOT NULL,
  INDEX post_ID (`post_ID`),
  INDEX user_ID (`user_ID`),
  INDEX ip_created_at (`ip`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";

		return $wpdb->query( $sql ) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;
		$sql = sprintf( "DROP TABLE IF EXISTS `%s`;", 'anycomment_uploaded_files');
		$wpdb->query( $sql );
	}
}