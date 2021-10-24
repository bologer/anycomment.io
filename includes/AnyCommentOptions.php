<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Migrations\AnyCommentMigration;

class AnyCommentOptions {

	/**
	 * Key to hold current migration of the plugin.
	 */
	const OPTION_MIGRATION = 'migration';

	/**
	 * @var string Default prefix for all options.
	 */
	public static $prefix = 'anycomment_';

	/**
	 * Get options value.
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param null $default Optional. Default value to return if the option does not exist.
	 *
	 * @return mixed
	 */
	public static function get( $option, $default = null ) {
		return get_option( static::prepare_option( $option ), $default );
	}

	/**
	 * Wrapper for WordPress add_option() to add new option.
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param mixed $value Optional. Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 *
	 * @return bool
	 */
	public static function add( $option, $value ) {
		return add_option( static::prepare_option( $option ), $value );
	}


	/**
	 * Wrapper for WordPress add_option() to add new option.
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param mixed $value Optional. Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @param bool $sanitizeValue Whether to sanitize incoming value.
	 *
	 * @return bool
	 */
	public static function update( $option, $value, $sanitizeValue = true ) {
		return update_option( static::prepare_option( $option ), $sanitizeValue ? sanitize_text_field( $value ) : $value );
	}

	/**
	 * Wrapper for WordPress delete_option() to delete option.
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 *
	 * @return bool
	 */
	public static function delete( $option ) {
		return delete_option( static::prepare_option( $option ) );
	}

	/**
	 * Prepare options name.
	 *
	 * @param string $option Option name.
	 *
	 * @return string
	 */
	public static function prepare_option( $option ) {
		if ( strpos( $option, self::$prefix ) == false ) {
			$option = sprintf( '%s%s', self::$prefix, $option );
		}

		return $option;
	}


	/**
	 * Helpers
	 */

	/**
	 * Update migration version.
	 *
	 * @param string $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 *
	 * @return bool
	 */
	public static function update_migration( $value ) {
		return static::update( self::OPTION_MIGRATION, AnyCommentMigration::normalize_version( $value ) );
	}

	/**
	 * Get current migration version.
	 *
	 * @return string
	 */
	public static function get_migration() {
		return static::get( self::OPTION_MIGRATION, '0.0.1' );
	}

	/**
	 * Delete migration.
	 *
	 * @return string
	 */
	public static function delete_migration() {
		return static::delete( self::OPTION_MIGRATION );
	}
}
