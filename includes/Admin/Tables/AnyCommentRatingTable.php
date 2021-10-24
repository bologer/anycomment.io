<?php

namespace AnyComment\Admin\Tables;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


use WP_User;
use AnyComment\Models\AnyCommentRating;

/**
 * Class AnyCommentUploadedFilesTable is used to display list of files in the admin.
 */
class AnyCommentRatingTable extends AnyCommentTable {
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
		$table = AnyCommentRating::get_table_name();

		global $wpdb;

		$countQuery = "SELECT COUNT(*) FROM $table";
		$count      = $wpdb->get_var( $countQuery );


		$orderby = $this->getOrderByParam( 'created_at' );
		$order   = $this->getOrderParam();

		$query = "SELECT * FROM {$table} ORDER BY {$orderby} {$order}";

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
		_e( 'No rating yet.', 'anycomment' );
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
					'action' => 'edit'
				), admin_url( 'post.php' ) ) );

				return sprintf( '<strong><a href="%s">%s</a></strong>', $url, $post->post_title );
			case 'user_ID':
				$user_object = get_user_by( 'id', $item[ $column_name ] );

				if ( ! $user_object instanceof WP_User ) {
					return '';
				}

				$super_admin = '';
				$user_html   = get_avatar( $user_object->ID, 25 );

				// Set up the user editing link
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_object->ID ) ) );

				if ( current_user_can( 'edit_user', $user_object->ID ) ) {
					$user_html       .= " <strong><a href=\"{$edit_link}\">{$user_object->user_login}</a>{$super_admin}</strong><br />";
					$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
				} else {
					$user_html .= " <strong>{$user_object->user_login}{$super_admin}</strong><br />";
				}

				return $user_html;
			case 'rating':
				return $item[ $column_name ];
			case 'ip':
				return $item[ $column_name ];
			case 'created_at':
				$format = sprintf( "%s %s", get_option( 'date_format' ), get_option( 'time_format' ) );

				return date( $format, $item[ $column_name ] );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ratings[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'anycomment' )
		);

		return $actions;
	}

	/**
	 * {@inheritdoc}
	 */
	function get_sortable_columns() {
		$sortable_columns = [
			'post_ID'    => [ 'post_ID', false ],
			'user_ID'    => [ 'user_ID', false ],
			'rating'     => [ 'rating', false ],
			'created_at' => [ 'created_at', false ]
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
			'user_ID'    => __( 'User', 'anycomment' ),
			'rating'     => __( 'Rating', 'anycomment' ),
			'ip'         => __( 'IP', 'anycomment' ),
			'created_at' => __( 'Date', 'anycomment' ),
		);

		return $c;
	}

	/**
	 * @inheritDoc
	 */
	protected function orderByColumns() {
		return [ 'created_at' ];
	}
}
