<?php

interface  AnyCommentMigrationInterface {
	public function isApplied();

	public function up();

	public function down();
}

class AnyCommentMigration implements AnyCommentMigrationInterface {
	public $prefix = 'anycomment_';

	public $table = null;

	/**
	 * @var array List of migration to apply. DESC order please (newest at the top).
	 *
	 * Format: 0.0.1, 0.0.2, etc
	 */
	private static $_list = [
		'0.0.3' => [ 'version' => '0.0.3', 'description' => 'Create likes table' ],
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
	 * Get list to migrate down.
	 *
	 * @param int $afterVersion Migration version
	 *
	 * @return array|null
	 */
	public static function getListDown( $afterVersion = null ) {
		$list = static::getList();

		if ( $list === null ) {
			return null;
		}

		if ( $afterVersion === null ) {
			return $list;
		}


		foreach ( $list as $key => $listVersion ) {
			if ( $listVersion != $afterVersion ) {
				unset( $list[ $key ] );
				continue;
			}

			break;
		}

		return $list;
	}

	/**
	 * Get list to migrate up direction.
	 *
	 * @param int $beforeVersion Migration version
	 *
	 * @return array|null NULL when migration list is empty.
	 */
	public static function getListUp( $beforeVersion = null ) {
		$list = static::getList();

		if ( $list === null ) {
			return null;
		}

		if ( $beforeVersion === null ) {
			return $list;
		}

		// 0.0.1, 0.0.2, etc
		$list = array_reverse( $list );

		foreach ( $list as $key => $listVersion ) {
			if ( $listVersion != $beforeVersion ) {
				unset( $list[ $key ] );
				continue;
			} else {
				unset( $list[ $key ] );
			}

			break;
		}

		return $list;
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

		$version = str_replace( '.', '', $version );

		return sprintf( "%'.03d", $version );
	}
}