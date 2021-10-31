<?php

use AnyComment\Models\AnyCommentRating;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Migration for `WP-PostRatings` plugin.
 */
global $wpdb;

$rating = $wpdb->get_results( "SELECT * FROM {$wpdb->ratings}" );

if ( ! empty( $rating ) ) {
	foreach ( $rating as $rate ) {

		if ( empty( $rate->rating_postid ) ) {
			continue;
		}

		$postId    = $rate->rating_postid;
		$userId    = empty( $rate->rating_userid ) ? 'NULL' : $rate->rating_userid;
		$rating    = $rate->rating_rating;
		$ip        = empty( $rate->rating_ip ) ? 'NULL' : $rate->rating_ip;
		$timestamp = $rate->rating_timestamp;

		$tableName = AnyCommentRating::table_name();

		$prepared_sql = $wpdb->prepare( "INSERT INTO $tableName (post_ID, user_ID, rating, ip, user_agent, created_at) VALUES (%d, %d, %f, %s, NULL, %d)", $postId, $userId, $rating, $ip, $timestamp );
		$isSuccess    = $wpdb->query( $prepared_sql );

		if ( $isSuccess ) {
			echo "<p>OK: " . esc_html( $rate->rating_id ) . "</p>";
		} else {
			echo "<p style='color: red;'>FAILED: " . esc_html( $rate->rating_id ) . "</p>";
		}

	}
}
