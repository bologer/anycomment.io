<?php

namespace AnyComment\Admin;

use AnyComment\AnyCommentCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * AC_Statistics helps to process statistics.
 */
class AnyCommentStatistics {
	/**
	 * Get number of active commentors in the system.
	 *
	 * @return int
	 */
	public static function get_commentor_count() {
		global $wpdb;

		return $wpdb->get_var( "SELECT COUNT(DISTINCT comments.user_id) AS count 
FROM $wpdb->comments AS comments 
RIGHT JOIN $wpdb->users AS users ON users.ID = comments.user_id
HAVING count >= 1" );
	}

	/**
	 * Get approved comment count.
	 *
	 * @return int
	 */
	public static function get_approved_comment_count() {
		global $wpdb;

		return $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE `comment_approved` = 1 AND `comment_type` IN ('', 'comment', 'review')" );
	}

	/**
	 * Get most active users. Most commenting ones.
	 *
	 * @param int $limit Number of comments to limit result.
	 *
	 * @return array|null|object
	 */
	public static function get_most_active_users( $limit = 6 ) {
		global $wpdb;

		$query = "SELECT users.ID AS user_id, users.`display_name` AS name, COUNT(comments.comment_ID) AS comment_count 
FROM $wpdb->comments AS comments 
RIGHT JOIN $wpdb->users AS users ON users.id = comments.user_id 
GROUP BY user_id
ORDER BY comment_count DESC 
LIMIT $limit";

		return $wpdb->get_results( $query );
	}

	/**
	 * Collects number of comments added per each day.
	 * @return array
	 */
	public static function get_comment_data() {
		global $wpdb;

		$query = "SELECT COUNT(comment_ID) as count, 
DATE_FORMAT(comment_date, '%d') as day,  
UNIX_TIMESTAMP(comment_date) as unix_timestamp
FROM $wpdb->comments 
WHERE MONTH(comment_date) = MONTH(NOW())
GROUP BY DAY(comment_date) 
ORDER BY day ASC";

		$item = AnyCommentCore::cache()->getItem( 'anycomment/statistics/get_comment_data' );

		if ( $item->isHit() ) {
			return $item->get();
		}

		$prepared_data = static::prepare_data( $query );

		$item->set( $prepared_data )
		     ->expiresAfter( 60 )
		     ->save();

		return $prepared_data;
	}

	/**
	 * Collects number of active users per each day
	 * that were actively commenting.
	 * @return array
	 */
	public static function get_commentor_data() {
		global $wpdb;

		$query = "SELECT COUNT(DISTINCT comments.user_id) as count, 
DATE_FORMAT(comments.comment_date, '%M %d') as day,
UNIX_TIMESTAMP(comments.comment_date) as unix_timestamp
FROM $wpdb->comments  AS comments
LEFT JOIN $wpdb->users AS users ON users.ID = comments.user_id
WHERE MONTH(comments.comment_date) = MONTH(NOW())
GROUP BY day
ORDER BY day ASC";

		$item = AnyCommentCore::cache()->getItem( 'anycomment/statistics/get_commentor_data' );

		if ( $item->isHit() ) {
			return $item->get();
		}

		$prepared_data = static::prepare_data( $query );

		$item->set( $prepared_data )
		     ->expiresAfter( 60 )
		     ->save();

		return $prepared_data;
	}

	/**
	 * Prepare data to be displayed in a chart.
	 *
	 * @param string $query Query to be executed.
	 * @param string $type Type of date. Default: month
	 *
	 * @return array See description below:
	 * - label - list of labels
	 * - data - list of data for labels
	 */
	public static function prepare_data( $query, $type = null ) {
		global $wpdb;

		$queryResult = $wpdb->get_results( $query );

		$labels = [];
		$data   = [];

		foreach ( $queryResult as $result ) {
			$labels[] = date_i18n( 'd F', esc_html( $result->unix_timestamp ) );
			$data[]   = $result->count;
		}

		// Default get number of days in a month
		if ( $type === null ) {
			$symbol = 't';
		}

		if ( $type !== null && $type == 'year' ) {
			$symbol = 'z';
		}

		// Fill 0 for empty days yet
		$daysInPeriod = date( $symbol );


		if ( count( $data ) < $daysInPeriod ) {
			$moreToAdd = $daysInPeriod - count( $data );

			for ( $i = 0; $i < $moreToAdd; $i ++ ) {
				$data[] = 0;
			}
		}

		// Return prepared for graph
		return [
			'label' => $labels,
			'data'  => $data
		];
	}
}
