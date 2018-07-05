<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
		return get_option( static::prepareOption( $option ), $default );
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
		return add_option( static::prepareOption( $option ), $value );
	}


	/**
	 * Wrapper for WordPress add_option() to add new option.
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param mixed $value Optional. Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 *
	 * @return bool
	 */
	public static function update( $option, $value ) {
		return update_option( static::prepareOption( $option ), $value );
	}

	/**
	 * Prepare options name.
	 *
	 * @param string $option Option name.
	 *
	 * @return string
	 */
	public static function prepareOption( $option ) {
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
	public static function updateMigration( $value ) {
		return static::update( self::OPTION_MIGRATION, $value );
	}

	/**
	 * Get current migration version.
	 *
	 * @return string
	 */
	public static function getMigration() {
		return static::get( self::OPTION_MIGRATION, '0.0.1' );
	}
}