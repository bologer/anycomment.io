<?php

namespace AnyComment\Admin\Tables;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Models\AnyCommentEmailQueue;

/**
 * Class AnyCommentEmailQueueTable is used to display list of emails in the queue in the admin.
 */
class AnyCommentEmailQueueTable extends AnyCommentTable {
	/**
	 * Site ID to generate the Users list table for.
	 *
	 * @since 3.1.0
	 * @var int
	 */
	public $site_id;

	/**
	 * Whether or not the current Users list table is for Multisite.
	 *
	 * @since 3.1.0
	 * @var bool
	 */
	public $is_site_users;

	/**
	 * Prepare the users list for display.
	 *
	 * @since 3.1.0
	 *
	 * @global string $role
	 * @global string $usersearch
	 */
	public function prepare_items() {
		global $wpdb;

		$table = AnyCommentEmailQueue::get_table_name();

		$countQuery = "SELECT COUNT(*) FROM $table";
		$count      = $wpdb->get_var( $countQuery );

		$orderby = $this->getOrderByParam( 'created_at' );
		$order   = $this->getOrderParam();
		$query   = "SELECT * FROM {$table} ORDER BY {$orderby} {$order}";

		$per_page     = 10;
		$current_page = $this->get_pagenum();

		$query .= $wpdb->prepare( " LIMIT %d, %d", $per_page * ( $current_page - 1 ), $per_page );

		$items = $wpdb->get_results( $query, 'ARRAY_A' );

		$this->items = $items;


		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$this->items = $items;

		$this->set_pagination_args( array(
			'total_items' => $count,
			'per_page'    => $per_page,
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	function no_items() {
		_e( 'No emails yet.', 'anycomment' );
	}

	/**
	 * {@inheritdoc}
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_ID':
				$post = get_post( $item[ $column_name ] );

				if ( $post === null ) {
					return null;
				}

				$url = esc_url( add_query_arg( array(
					'post'   => $post->ID,
					'action' => 'edit',
				), admin_url( 'post.php' ) ) );

				return sprintf( '<strong><a href="%s">%s</a></strong>', $url, $post->post_title );
			case 'comment_ID':
				$comment = get_comment( $item[ $column_name ] );

				if ( ! $comment instanceof \WP_Comment ) {
					return '';
				}

				// Set up the user editing link
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_comment_to_edit( $comment->ID ) ) );

				if ( current_user_can( 'moderate_comments', $comment->ID ) ) {
					$comment_html = $comment->comment_content . "<br>";
					$comment_html .= "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' aria-label='" . esc_attr__( 'Edit this comment' ) . "'>" . __( 'Edit' ) . '</a>';;
				} else {
					$comment_html = " <strong>{$comment->comment_content}</strong><br />";
				}

				return $comment_html;
			case 'email':

				$email = $item[ $column_name ];

				if ( empty( $email ) ) {
					return '';
				}

				$user = get_user_by( 'email', $email );

				$to_display = $user instanceof \WP_User ? $user->user_login : $email;

				$super_admin = '';
				$user_html   = '';

				if ( $user instanceof \WP_User ) {
					$user_html = get_avatar( $user->ID, 25 );
				}

				// Set up the user editing link
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user->ID ) ) );

				if ( current_user_can( 'edit_user' ) ) {
					$user_html       .= " <strong><a href=\"{$edit_link}\">{$to_display}</a>{$super_admin}</strong><br />";
					$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
				} else {
					$user_html .= " <strong>{$to_display}{$super_admin}</strong><br />";
				}

				return $user_html;
			case 'subject':
				return $item[ $column_name ];
			case 'ip':
				return $item[ $column_name ];
			case 'url':
				return sprintf( '<a href="%s" target="_blank">%s</a>', $item[ $column_name ], $item[ $column_name ] );
			case 'is_sent':
				return $item[ $column_name ];
			case 'created_at':
				$format = sprintf( "%s %s", get_option( 'date_format' ), get_option( 'time_format' ) );

				return date( $format, strtotime( $item[ $column_name ] ) );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="emails[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'anycomment' ),
		);

		return $actions;
	}

	/**
	 * {@inheritdoc}
	 */
	function get_sortable_columns() {
		$sortable_columns = [
			'post_ID'    => [ 'post_ID', false ],
			'comment_ID' => [ 'comment_ID', false ],
			'is_sent'    => [ 'is_sent', false ],
			'ip'         => [ 'ip', false ],
			'created_at' => [ 'created_at', false ],
		];

		return $sortable_columns;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns() {
		$c = array(
			'cb'         => '<input type="checkbox" />',
			'post_ID'    => __( 'Post', 'anycomment' ),
			'comment_ID' => __( 'Comment', 'anycomment' ),
			'email'      => __( 'Email', 'anycomment' ),
			'subject'    => __( 'Subject', 'anycomment' ),
			'ip'         => __( 'IP', 'anycomment' ),
			'is_sent'    => __( 'Is Sent?', 'anycomment' ),
			'created_at' => __( 'Date', 'anycomment' ),
		);

		return $c;
	}

	protected function orderByColumns() {
		return [
			'created_at',
			'is_sent'
		];
	}
}
