<?php

/**
 * Class AnyCommentWPComments is used to add custom columns, actions, etc related to native
 * comments in WordPress.
 *
 * @since 0.0.35
 */
class AnyCommentWPComments {

	public function __construct() {
		$this->init();
	}

	private function init() {
		add_filter( 'manage_edit-comments_columns', [ $this, 'manage_comment_columns' ] );
		add_action( 'manage_comments_custom_column', [ $this, 'add_comment_columns' ], null, 2 );
	}

	public function manage_comment_columns( $columns ) {
		$columns['anycomment-likes'] = __( 'Likes', 'anycomment' );

		return $columns;
	}

	public function add_comment_columns( $column_name, $comment_id ) {
		if ( $column_name == 'anycomment-likes' ) {
			echo AnyCommentLikes::getLikesCount( $comment_id );
		}
	}
}