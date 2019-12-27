<?php

namespace AnyComment\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Base\BaseObject;
use AnyComment\Models\AnyCommentLikes;
use AnyComment\AnyCommentUserMeta;

/**
 * Class AnyCommentWPComments is used to add custom columns, actions, etc related to native
 * comments in WordPress.
 *
 * @since 0.0.35
 */
class AnyCommentWPComments extends BaseObject {

	public function init() {
		add_filter( 'manage_edit-comments_columns', [ $this, 'manage_comment_columns' ] );
		add_action( 'manage_comments_custom_column', [ $this, 'add_comment_columns' ], null, 2 );

		add_action( 'manage_users_columns', [ $this, 'add_likes_column' ] );
		add_action( 'manage_users_custom_column', [ $this, 'add_user_columns' ], null, 3 );
	}

	/**
	 * Add likes column in comments (`edit-comments.php`).
	 *
	 * @parama array $columns List of columns.
	 *
	 * @return mixed
	 */
	public function manage_comment_columns( $columns ) {
		$columns['anycomment_likes'] = __( 'Likes', 'anycomment' );

		return $columns;
	}

	/**
	 * Display likes in the column of comments table.
	 *
	 * @param string $column_name Column name.
	 * @param int $comment_id Comment id.
	 */
	public function add_comment_columns( $column_name, $comment_id ) {
		if ( $column_name == 'anycomment_likes' ) {
			echo AnyCommentLikes::get_likes_count( $comment_id );
		}
	}

	/**
	 * Add likes column to users (`users.php`).
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function add_likes_column( $columns ) {
		$columns['anycomment_likes_count'] = __( "Likes", "anycomment" );
		$columns['anycomment_social_url']  = __( "Social URL", "anycomment" );

		return $columns;
	}

	/**
	 * Add likes count in column in users table.
	 *
	 * @param string $column_name Column name.
	 * @param string $value
	 * @param int $user_id User ID
	 *
	 * @return int Number of likes.
	 */
	public function add_user_columns( $value, $column_name, $user_id ) {
		if ( $column_name == 'anycomment_likes_count' ) {
			return AnyCommentLikes::get_likes_count_by_user( $user_id );
		} elseif ( $column_name == 'anycomment_social_url' ) {
			$socialUrl = AnyCommentUserMeta::get_social_profile_url( $user_id, true );

			return ! empty( $socialUrl ) ? $socialUrl : '—';
		}
	}
}
