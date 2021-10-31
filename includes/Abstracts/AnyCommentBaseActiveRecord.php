<?php

namespace AnyComment\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentBaseActiveRecord serves as a base class for all models connected to plugin's table.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Abstracts
 */
abstract class AnyCommentBaseActiveRecord {
	/**
	 * @var string Table name.
	 */
	protected static $table_name;

	/**
	 * @var string Table name prefix.
	 */
	protected static $table_project_prefix = 'anycomment';

	/**
	 * Get table name.
	 *
	 * Format: {wp_prefix}_{project_prefix}_{table_name}
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;

		return sprintf( "%s%s_%s", $wpdb->prefix, static::$table_project_prefix, static::$table_name );
	}

	/**
	 * Delete all by specified .
	 *
	 * @param string $column Column to be searched by search.
	 * @param array|string|int $search Search value. Would be used in IN(). Can be list of IDs.
	 *
	 * @return bool|false|int
	 */
	public static function deleted_all( $column, $search ) {
		global $wpdb;

		if ( empty( $search ) ) {
			return false;
		}

		$prepared_ids = [];

		if ( is_numeric( $search ) ) {
			$prepared_ids = $wpdb->prepare( '%d', $search );
		} else if ( is_array( $search ) ) {
			$prepared_ids = array_map( function ( $id ) use ( $wpdb ) {
				return $wpdb->prepare( '%d', $id );
			}, $search );
			$prepared_ids = implode( ',', $prepared_ids );
		}

		if ( empty( $prepared_ids ) ) {
			return false;
		}

		$table = static::get_table_name();

		$prepared_sql = $wpdb->prepare( "DELETE FROM $table WHERE $column IN ($prepared_ids)" );

		return $wpdb->query( $prepared_sql );
	}
}
