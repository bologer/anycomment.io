<?php

class AnyCommentMigration_0_0_61 extends AnyCommentMigration {
	public $table   = 'rating';
	public $version = '0.0.61';

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
		$sql = "CREATE TABLE `{$this->getTable()}` (
				  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `post_ID` bigint(20) UNSIGNED NOT NULL,
				  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
				  `rating` smallint(1) SIGNED DEFAULT 5,
				  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `created_at` bigint(20) NOT NULL,
				  INDEX post_ID (`post_ID`),
				  INDEX user_ID (`user_ID`),
				  FOREIGN KEY (post_ID)
				        REFERENCES {$wpdb->posts}(ID)
				        ON DELETE CASCADE,
				  FOREIGN KEY (user_ID)
				        REFERENCES {$wpdb->users}(ID)
				        ON DELETE CASCADE
				) $charset_collate;";

		return $wpdb->query( $sql ) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS `{$this->getTable()}`;";
		$wpdb->query( $sql );
	}
}

// eof;
