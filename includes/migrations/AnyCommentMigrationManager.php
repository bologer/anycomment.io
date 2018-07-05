<?php

include_once( ANY_COMMENT_ABSPATH . 'includes/migrations/AnyCommentMigration.php' );

/**
 * Class AnyCommentMigrationManager
 */
class AnyCommentMigrationManager {

	/**
	 * @var string Key to retrieve latest applied migration.
	 */
	public static $optionName = 'anycomment-migration';

	/**
	 * Get migration file format.
	 *
	 * @return string
	 */
	public function getFileFormat() {
		return 'AnyCommentMigration%s';
	}

	/**
	 * Apply all migrations.
	 */
	public function applyAll() {
		$currentMigrationVersion = AnyCommentOptions::getMigration();
		$migrationList           = AnyCommentMigration::getListUp( $currentMigrationVersion );

		if ( $migrationList === null ) {
			return true;
		}

		foreach ( $migrationList as $key => $migrationVersion ) {
			$format        = $this->getFileFormat();
			$migrationName = sprintf( $format, $migrationVersion );
			$path          = sprintf( ANY_COMMENT_ABSPATH . 'includes/migrations/%s.php', $migrationName );

			if ( ! file_exists( $path ) ) {
				continue;
			}

			include_once( $path );

			/**
			 * @var $model AnyCommentMigration
			 */
			$model = new $migrationName();

			if ( ! $model->isApplied() ) {
				if ( $model->up() ) {
					AnyCommentOptions::updateMigration( $migrationVersion );
				}
			}
		}

		return true;
	}

	/**
	 * Drop all applied migrations.
	 *
	 * @return bool
	 */
	public function dropAll() {
		$currentMigrationVersion = AnyCommentOptions::getMigration();
		$migrationList           = AnyCommentMigration::getListDown( $currentMigrationVersion );

		if ( empty( $migrationList ) ) {
			return true;
		}

		foreach ( $migrationList as $key => $migrationVersion ) {
			$format        = $this->getFileFormat();
			$migrationName = sprintf( $format, $migrationVersion );
			$path          = sprintf( ANY_COMMENT_ABSPATH . 'includes/migrations/%s.php', $migrationName );

			if ( ! file_exists( $path ) ) {
				continue;
			}

			include_once( $path );

			/**
			 * @var $model AnyCommentMigration
			 */
			$model = new $migrationName();

			if ( $model->isApplied() ) {
				if ( $model->down() ) {
					AnyCommentOptions::updateMigration( $migrationVersion );
				}
			}
		}

		return true;
	}
}