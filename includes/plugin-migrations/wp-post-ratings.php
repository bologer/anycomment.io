<?php

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

		$isSuccess = $wpdb->query( "INSERT INTO $tableName (post_ID, user_ID, rating, ip, user_agent, created_at) VALUES ($postId, $userId, $rating, '$ip', NULL, $timestamp)" );

		if ( $isSuccess ) {
			echo "<p>OK: {$rate->rating_id}</p>";
		} else {
			echo "<p style='color: red;'>FAILED: {$rate->rating_id}</p>";
		}

	}
}
?>