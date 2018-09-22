<?php

/**
 * Class AnyCommentMigration_0_0_56 is used to re upload big avatars and make them smaller in favor of speed.
 */
class AnyCommentMigration_0_0_57 extends AnyCommentMigration {
	public $table = 'uploaded_files';
	public $version = '0.0.57';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;
		$queryRes = $wpdb->get_results( "SHOW COLUMNS FROM `{$this->getTable()}` LIKE 'url_thumbnail';", 'ARRAY_A' );

		return $queryRes !== null && count( $queryRes ) > 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$sql = "ALTER TABLE `{$this->getTable()}` ADD COLUMN `url_thumbnail` VARCHAR(255) NULL";

		return $wpdb->query( $sql ) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$sql = "ALTER TABLE `{$this->getTable()}` DROP COLUMN `url_thumbnail`";

		return $wpdb->query( $sql ) !== false;
	}
}