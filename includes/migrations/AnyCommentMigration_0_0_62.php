<?php

class AnyCommentMigration_0_0_62 extends AnyCommentMigration {
	public $table = 'rating';
	public $version = '0.0.62';

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

		/**
		 * Create email queue table
		 */
		$sql = "CREATE TABLE IF NOT EXISTS `{$this->getTable()}` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_ID` bigint(20) UNSIGNED NOT NULL,
  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` smallint(1) SIGNED DEFAULT 5,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		$table_created = $wpdb->query( $sql ) !== false;
		$count         = 0;
		if ( $table_created ) {
			$indexes = [
				"ALTER TABLE `{$this->getTable()}` ADD INDEX `post_ID` (`post_ID`)",
				"ALTER TABLE `{$this->getTable()}` ADD INDEX `user_ID` (`user_ID`)"
			];

			foreach ( $indexes as $index_query ) {
				if ( $wpdb->query( $index_query ) !== false ) {
					$count ++;
				}
			}

			return count( $indexes ) == $count;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}
}