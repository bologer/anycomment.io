<?php

/**
 * Class AnyCommentMigration_0_0_57 is used to re upload big avatars and make them smaller in favor of speed.
 */
class AnyCommentMigration_0_0_57 extends AnyCommentMigration {
	public $table = 'uploaded_files';
	public $version = '0.0.57';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;
		$queryRes  = $wpdb->get_results( "SHOW COLUMNS FROM `{$this->getTable()}` LIKE 'url_thumbnail';", 'ARRAY_A' );
		$queryRes2 = $wpdb->get_results( "SHOW COLUMNS FROM `{$this->getTable()}` LIKE 'type';", 'ARRAY_A' );

		return ! empty( $queryRes ) && count( $queryRes ) > 0 &&
		       ! empty( $queryRes2 ) && count( $queryRes2 ) > 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$sql  = "ALTER TABLE `{$this->getTable()}` ADD COLUMN `url_thumbnail` VARCHAR(255) NULL";
		$sql2 = "ALTER TABLE `{$this->getTable()}` ADD COLUMN `type` VARCHAR(255) NOT NULL";


		$success = $wpdb->query( $sql ) !== false &&
		           $wpdb->query( $sql2 ) !== false;

		$sql3 = "SELECT * FROM `{$this->getTable()}` WHERE `type`='' OR `type` IS NULL";

		$rows = $wpdb->get_results( $sql3 );

		if ( ! empty( $rows ) ) {
			/**
			 * @var AnyCommentUploadedFiles $row
			 */
			foreach ( $rows as $key => $row ) {
				$extension = pathinfo( $row->url, PATHINFO_EXTENSION );

				if ( empty( $extension ) ) {
					continue;
				}

				$mime_to_use = null;
				$mime_types  = wp_get_mime_types();

				foreach ( $mime_types as $extensions => $mime_type ) {
					if ( strpos( $extensions, $extension ) !== false ) {
						$mime_to_use = $mime_types[ $extensions ];
					}
				}

				if ( $mime_to_use !== null ) {
					$wpdb->update( $this->getTable(), [ 'type' => $mime_to_use ], [ 'id' => $row->ID ] );
				}
			}
		}

		return $success;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$sql  = "ALTER TABLE `{$this->getTable()}` DROP COLUMN `url_thumbnail`";
		$sql2 = "ALTER TABLE `{$this->getTable()}` DROP COLUMN `type`";

		return $wpdb->query( $sql ) !== false &&
		       $wpdb->query( $sql2 ) !== false;
	}
}