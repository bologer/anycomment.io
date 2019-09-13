<?php

namespace AnyComment\Migrations;

use AnyComment\AnyCommentOptions;

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
	public function get_file_format() {
		return 'AnyCommentMigration_%s';
	}

	/**
	 * Apply all migrations.
	 */
	public function apply_all() {
		$migrationList = AnyCommentMigration::get_list_up();

		if ( empty($migrationList) ) {
			return true;
		}

		foreach ( $migrationList as $key => $migrationVersion ) {
			$format        = $this->get_file_format();
			$migrationName = sprintf( $format, $migrationVersion );
			$path          = sprintf( __DIR__ . '/%s.php', $migrationName );

			if ( ! file_exists( $path ) ) {
				continue;
			}

			include_once( $path );

			/**
			 * @var $model AnyCommentMigration
			 */
			$namespace = "\AnyComment\Migrations\\$migrationName";
			$model     = new $namespace;

			if ( ! $model->is_applied() && $model->up() ) {
				AnyCommentOptions::update_migration( $migrationVersion );
			} elseif ( $model->is_applied() ) {
				AnyCommentOptions::update_migration( $migrationVersion );
			}
		}

		return true;
	}

	/**
	 * Drop all applied migrations.
	 *
	 * @return bool
	 */
	public function drop_all() {
		$migrationList = AnyCommentMigration::get_list_down();

		if ( empty( $migrationList ) ) {
			return true;
		}

		$migrationList = array_reverse($migrationList);

		foreach ( $migrationList as $key => $migrationVersion ) {
			$format        = $this->get_file_format();
			$migrationName = sprintf( $format, $migrationVersion );
			$path          = sprintf( rtrim(ANYCOMMENT_ABSPATH, '/\\') . '/includes/Migrations/%s.php', $migrationName );

			if ( ! file_exists( $path ) ) {
				continue;
			}

			include_once( $path );

            $namespace = "\AnyComment\Migrations\\$migrationName";
			/**
			 * @var $model AnyCommentMigration
			 */
			$model = new $namespace();

			if ( $model->is_applied() && $model->down() ) {
				AnyCommentOptions::update_migration( $migrationVersion );
			}
		}

		return true;
	}
}