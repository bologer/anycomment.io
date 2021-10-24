<?php

namespace AnyComment\Admin\Tables;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Models\AnyCommentSubscriptions;
use WP_User;


/**
 * Class AnyCommentSubscribersTable is used to display list of subscribers as WP native table in the admin.
 *
 * @since 0.0.70
 */
class AnyCommentSubscriptionsTable extends AnyCommentTable {
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
		$table = AnyCommentSubscriptions::get_table_name();

		global $wpdb;
		$countQuery = "SELECT COUNT(*) FROM $table";
		$count      = $wpdb->get_var( $countQuery );

		$orderby = $this->getOrderByParam('created_at');
		$order = $this->getOrderParam();

		$query = "SELECT * FROM $table ORDER BY $orderby $order";

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
		_e( 'No subscribers yet.', 'anycomment' );
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
					return $item['email'];
				}

				$super_admin = '';
				$userHtml    = get_avatar( $user_object->ID, 25 );

				// Set up the user editing link
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_object->ID ) ) );

				if ( current_user_can( 'edit_user', $user_object->ID ) ) {
					$userHtml        .= " <strong><a href=\"{$edit_link}\">{$user_object->user_login}</a>{$super_admin}</strong><br />";
					$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
				} else {
					$userHtml .= " <strong>{$user_object->user_login}{$super_admin}</strong><br />";
				}

				return $userHtml;
			case 'is_active':
				return $item[ $column_name ];
			case 'confirmed_at':
				$format = sprintf( "%s %s", get_option( 'date_format' ), get_option( 'time_format' ) );

				return date( $format, $item[ $column_name ] );
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
			'<input type="checkbox" name="subscriptions[]" value="%s" />', $item['ID']
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
			'post_ID'      => [ 'post_ID', false ],
			'user_ID'      => [ 'user_ID', false ],
			'is_active'    => [ 'is_active', false ],
			'confirmed_at' => [ 'confirmed_at', false ],
			'created_at'   => [ 'created_at', false ]
		];

		return $sortable_columns;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns() {
		$c = array(
			'cb'           => '<input type="checkbox" />',
			'post_ID'      => __( 'Post', 'anycomment' ),
			'user_ID'      => __( 'User', 'anycomment' ),
			'is_active'    => __( 'Is Active?', 'anycomment' ),
			'confirmed_at' => __( 'Confirmation', 'anycomment' ),
			'created_at'   => __( 'Date', 'anycomment' ),
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
