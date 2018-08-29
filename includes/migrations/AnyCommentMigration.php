<?php

interface  AnyCommentMigrationInterface {
	public function isApplied();

	public function up();

	public function down();
}

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
		'0.0.50' => [
			'version'     => '0.0.50',
			'description' => 'Added "subject", "email" columns and modified "sent_at" to be NULL by default in "email_queue" table'
		],
		'0.0.52' => [
			'version'     => '0.0.52',
			'description' => 'Create "uploaded_files" table to store log history of all uploaded files'
		],
	];

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
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

		return sprintf( "%s%s", $this->prefix, $this->table );
	}

	/**
	 * Get list of migrations to apply.
	 *
	 * @param bool $clean If required to get clean version names.
	 *
	 * @return array|null NULL when migration list is empty.
	 */
	public static function getList( $clean = true ) {

		$list = self::$_list;

		if ( empty( $list ) ) {
			return null;
		}

		$list = array_keys( $list );

		if ( ! $clean ) {
			return $list;
		}

		foreach ( $list as $key => $version ) {
			$list[ $key ] = static::cleanVersion( $version );
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
	public static function cleanVersions( $versions ) {
		if ( ! empty( $versions ) ) {
			foreach ( $versions as $key => $version ) {
				$versions[ $key ] = static::cleanVersion( $version );
			}
		}

		return $versions;
	}

	/**
	 * Get list to migrate down.
	 *
	 * @return array|null
	 */
	public static function getListDown() {
		$currentVersion = AnyCommentOptions::getMigration();
		$list           = static::getList( false );

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

			break;
		}

		return static::cleanVersions( $list );
	}

	/**
	 * Get list to migrate up direction.
	 *
	 * @return array|null NULL when migration list is empty.
	 */
	public static function getListUp() {
		$currentVersion = AnyCommentOptions::getMigration();
		$list           = static::getList( false );

		if ( $list === null ) {
			return null;
		}

		if ( $currentVersion === null ) {
			return $list;
		}

		// 0.0.1, 0.0.2, etc
		$list = array_reverse( $list );

		foreach ( $list as $key => $listVersion ) {
			if ( version_compare( $listVersion, $currentVersion, '<=' ) ) {
				unset( $list[ $key ] );
				continue;
			}

			break;
		}

		return static::cleanVersions( $list );
	}

	/**
	 * Convert version to int.
	 * E.g. 0.0.1 to 001.
	 *
	 * @param int $version Version to be converted to integer.
	 *
	 * @return string|false String on success and false on failure.
	 */
	public static function cleanVersion( $version ) {
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
	public static function normalizeVersion( $version ) {
		if ( strpos( $version, '_' ) === false ) {
			return $version;
		}

		return str_replace( '_', '.', $version );
	}
}