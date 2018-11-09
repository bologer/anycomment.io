<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_User;
use WP_Comment;

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Rest\AnyCommentSocialAuth;

class AnyCommentUser {

	/**
	 * Get user prepared for react output.
	 *
	 * @return null|WP_User
	 */
	public static function getSafeUser() {
		$user = wp_get_current_user();

		if ( $user->ID == 0 ) {
			return null;
		}

		unset( $user->data->user_activation_key );
		unset( $user->data->user_email );
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
	public static function get_comment_count( $post_id ) {
		$count = get_comments_number( $post_id );

		return sprintf( _nx( '%s comment', '%s comments', $count, 'REST API Comments Count', 'anycomment' ), number_format_i18n( $count ) );
	}

	/**
	 * Check whether it is too old to edit (update/delete) comment.
	 *
	 * @param WP_Comment $comment Comment to be checked.
	 *
	 * @return bool
	 */
	public static function is_old_to_edit( $comment ) {
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
	public static function can_edit_comment( $comment ) {
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
}