<?php

class AnyCommentMigration0021 extends AnyCommentMigration {
	public $table = 'likes';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;

		$res = $wpdb->get_results( "SHOW TABLES LIKE '{$this->getTable()}';", 'ARRAY_A' );

		if ( $res === null || count( $res ) > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$table = $this->getTable();


		$sql = "CREATE TABLE `$table` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `post_ID` bigint(20) UNSIGNED NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `liked_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";

		if ( $wpdb->query( $sql ) !== false ) {

			$user_id_index     = "ALTER TABLE `$table` ADD INDEX `user_ID` (`user_ID`)";
			$post_id_index     = "ALTER TABLE `$table` ADD INDEX `post_ID` (`post_ID`)";

			$wpdb->query( $user_id_index );
			$wpdb->query( $post_id_index );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$sql = "DROP TABLE IF EXISTS `{$this->getTable()}`;";
		$wpdb->query( $sql );

		return true;
	}
}