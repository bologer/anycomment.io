<?php

namespace AnyComment\Migrations;

use AnyComment\AnyCommentOptions;
use AnyComment\Interfaces\AnyCommentMigrationInterface;

class AnyCommentMigration implements AnyCommentMigrationInterface {
	public $prefix = 'anycomment_';

	public $table = null;
	public $version = null;

	/**
	 * @var array List of migration to apply. DESC order please (newest at the top).
	 *
	 * Format: 0.0.1, 0.0.2, etc
	 */
	private static $_list = [
		'0.0.32' => [ 'version' => '0.0.32', 'description' => 'Create likes table' ],
		'0.0.45' => [
			'version'     => '0.0.45',
			'description' => 'VK option name change from short "vk" to "vkontake" & creation of "email_queue" table to keep email to send'
		],
		'0.0.52' => [
			'version'     => '0.0.52',
			'description' => 'Create "uploaded_files" table to store log history of all uploaded files'
		],
		'0.0.56' => [
			'version'     => '0.0.56',
			'description' => 'Reupload avatars to make them smaller in size and therefore speed-up comments loading'
		],
		'0.0.59' => [
			'version'     => '0.0.59',
			'description' => 'Create anycomment_email_queue table if does not exist and prefix every custom table from plugin with WordPress\'s default wpdb prefix'
		],
		'0.0.61' => [
			'version'     => '0.0.61',
			'description' => 'Create anycomment_rating table for keeping rating information'
		],
		'0.0.68' => [
			'version'     => '0.0.68',
			'description' => 'Create anycomment_subscription table to keep track of who is subscribing for comments'
		],
		'0.0.70' => [
			'version'     => '0.0.70',
			'description' => 'Migrate existing social users to new username format'
		],
		'0.0.88' => [
			'version'     => '0.0.88',
			'description' => 'Clean-up generic options from infinite backslashes added by safe WordPress POST'
		],
	];

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}

	/**
	 * Get table name.
	 *
	 * @return null|string
	 */
	protected function getTable() {
		if ( $this->table === null ) {
			return null;
		}

		global $wpdb;

		return sprintf( '%s%s%s', $wpdb->prefix, $this->prefix, $this->table );
	}

	/**
	 * Get list of migrations to apply.
	 *
	 * @param bool $clean If required to get clean version names.
	 *
	 * @return array|null NULL when migration list is empty.
	 */
	public static function get_list( $clean = true ) {

		$list = self::$_list;

		if ( empty( $list ) ) {
			return null;
		}

		$list = array_keys( $list );

		if ( ! $clean ) {
			return $list;
		}

		foreach ( $list as $key => $version ) {
			$list[ $key ] = static::get_clean_version( $version );
		}

		return $list;
	}

	/**
	 * Clean versions.
	 *
	 * @param array $versions List of versions.
	 *
	 * @return mixed
	 */
	public static function get_clean_versions( $versions ) {
		if ( ! empty( $versions ) ) {
			foreach ( $versions as $key => $version ) {
				$versions[ $key ] = static::get_clean_version( $version );
			}
		}

		return $versions;
	}

	/**
	 * Get list to migrate down.
	 *
	 * @return array|null
	 */
	public static function get_list_down() {
		$currentVersion = AnyCommentOptions::get_migration();
		$list           = static::get_list( false );

		if ( $list === null ) {
			return null;
		}

		if ( $currentVersion === null ) {
			return $list;
		}

		// 0.0.2, 0.0.1


		foreach ( $list as $key => $listVersion ) {
			if ( version_compare( $currentVersion, $listVersion, '<' ) ) {
				unset( $list[ $key ] );
				continue;
			}
		}

		return static::get_clean_versions( $list );
	}

	/**
	 * Get list to migrate up direction.
	 *
	 * @return array|null NULL when migration list is empty.
	 */
	public static function get_list_up() {
		$currentVersion = AnyCommentOptions::get_migration();
		$list           = static::get_list( false );

		if ( $list === null ) {
			return null;
		}

		if ( $currentVersion === null ) {
			return $list;
		}

		foreach ( $list as $key => $listVersion ) {
			if ( version_compare( $listVersion, $currentVersion, '<' ) ) {
				unset( $list[ $key ] );
				continue;
			}

			break;
		}

		return static::get_clean_versions( $list );
	}

	/**
	 * Convert version to int.
	 * E.g. 0.0.1 to 001.
	 *
	 * @param int $version Version to be converted to integer.
	 *
	 * @return string|false String on success and false on failure.
	 */
	public static function get_clean_version( $version ) {
		if ( empty( $version ) ) {
			return false;
		}

		$version = str_replace( '.', '_', $version );

		return $version;
	}

	/**
	 * Normalize version back to normal.
	 *
	 * @param string $version Version to be normalized.
	 *
	 * @return string
	 */
	public static function normalize_version( $version ) {
		if ( strpos( $version, '_' ) === false ) {
			return $version;
		}

		return str_replace( '_', '.', $version );
	}
}