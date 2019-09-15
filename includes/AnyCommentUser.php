<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_User;
use WP_Comment;

use AnyComment\Rest\AnyCommentSocialAuth;
use AnyComment\Helpers\AnyCommentInflector;
use AnyComment\Admin\AnyCommentGenericSettings;

class AnyCommentUser {

	/**
	 * Get user prepared for react output.
	 *
	 * @return null|WP_User
	 */
	public static function getSafeUser () {
		$user = wp_get_current_user();

		if ( $user->ID == 0 ) {
			return null;
		}

		unset( $user->data->user_activation_key );
		unset( $user->data->user_pass );
		unset( $user->data->user_registered );
		unset( $user->data->user_status );

		$user->user_avatar = AnyCommentSocialAuth::get_user_avatar_url( $user->ID );

		return $user;
	}

	/**
	 * Get comment count.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	public static function get_comment_count ( $post_id ) {
		$count = !empty($post_id) ? get_comments_number( $post_id ) : 0;

		return sprintf( _nx( '%s comment', '%s comments', $count, 'REST API Comments Count', 'anycomment' ), number_format_i18n( $count ) );
	}

	/**
	 * Check whether it is too old to edit (update/delete) comment.
	 *
	 * @param WP_Comment $comment Comment to be checked.
	 *
	 * @return bool
	 */
	public static function is_old_to_edit ( $comment ) {
		$commentTime = strtotime( $comment->comment_date_gmt );

		$minutes = AnyCommentGenericSettings::get_comment_update_time();

		if ( $minutes === 0 ) {
			return false;
		}

		$secondsToEdit        = (int) $minutes * 60;
		$currentUnixTimeMysql = strtotime( current_time( 'mysql', true ) );

		return $currentUnixTimeMysql > ( $commentTime + $secondsToEdit );
	}

	/**
	 * Check whether current user has ability to edit comment.
	 *
	 * @param WP_Comment $comment
	 *
	 * @return bool
	 */
	public static function can_edit_comment ( $comment ) {
		if ( current_user_can( 'moderate_comments' ) ||
		     current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return true;
		}

		if ( static::is_old_to_edit( $comment ) ) {
			return false;
		}

		$user = wp_get_current_user();

		if ( ! $user instanceof WP_User ) {
			return false;
		}

		return (int) $user->ID === (int) $comment->user_id;
	}

	/**
	 * Prepares username or first/last name to become unique and pretty username.
	 *
	 * @param string $expected_username Could be username or first/last name which will be converted into username.
	 *
	 * @return string
	 * @since 0.0.70
	 */
	public static function prepare_login ( $expected_username ) {

		// Transliterate to remove junky chars
		$prepared_username = AnyCommentInflector::transliterate( $expected_username );

		// Extra sanitize username
		$prepared_username = sanitize_user( $prepared_username );

		// Lowercase username
		$prepared_username = strtolower( $prepared_username );

		// Clean-up anything that is not A-Z, 0-9 or underscore
		$prepared_username = preg_replace( '/[^A-Za-z0-9_\s]/', '', $prepared_username );

		// Replace spaces with "_"
		$prepared_username = preg_replace( '/\s+/', '_', $prepared_username );

		// Replace more then one of "_" with once of such type
		$prepared_username = preg_replace( '/_{2,}/', '_', $prepared_username );

		// Wne username is empty, should create some random name in order to have at least something
		if ( empty( $prepared_username ) ) {
			$prepared_username = 'user' . time();
		}

		/**
		 * Make username unique by adding extra numbers after it if exists already
		 */
		$unique_username = $prepared_username;
		$i               = 0;

		do {
			$user = get_user_by( 'login', $unique_username );

			if ( $user !== false ) {
				// Remove previous _{n}, so we can try new ID
				$unique_username = preg_replace( '/(_\d{1,})$/', '', $unique_username );
				// Append new ID to the username and try the search again
				$unique_username .= '_' . $i;
			}

			$i ++;

		} while ( $user !== false );

		/**
		 * Converts first or last name into proper username.
		 *
		 * @since 0.0.70
		 *
		 * @param string $unique_username Username that was produced after transliteration and sanitation via sanitize_user().
		 * @param string $expected_username Initial value passed. It could be username already, first/last name.
		 */
		return apply_filters( 'anycomment/user/prepare_login', $unique_username, $expected_username );
	}

	/**
	 * Get comment count by specified user id or email.
	 *
	 * @param int|string $id_or_email
	 * @param bool $only_approved Whether to count only approved or not.
	 *
	 * @return int
	 */
	public static function get_comment_count_by_user ( $id_or_email, $only_approved = false ) {

		$field = '';

		if ( is_string( $id_or_email ) ) {
			$field = 'email';
		} elseif ( is_numeric( $id_or_email ) ) {
			$field = 'id';
		}

		if ( empty( $field ) ) {
			return 0;
		}

		$user = get_user_by( $field, $id_or_email );

		// When no user found and field is not email (as this is the only field we can use to search for guest users
		// should return 0
		if ( ! $user && $field !== 'email' ) {
			return 0;
		}

		$db_field       = $user instanceof WP_User ? 'user_id' : 'comment_author_email';
		$db_field_value = $user instanceof WP_User ? $user->ID : $id_or_email;

		global $wpdb;

		$sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE $db_field = %s", $db_field_value );

		if ( $only_approved ) {
			$sql .= ' AND `comment_approved` = 1';
		}

		return (int) $wpdb->get_var( $sql );
	}
}
