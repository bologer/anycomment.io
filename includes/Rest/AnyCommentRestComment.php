<?php

namespace AnyComment\Rest;

use AnyComment\AnyCommentUser;
use AnyComment\Cache\AnyCommentRestCacheManager;
use WP_Post;
use WP_User;
use WP_Error;
use WP_Comment;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;
use WP_Comment_Query;
use WP_REST_Posts_Controller;

use AnyComment\Models\AnyCommentLikes;
use AnyComment\AnyCommentComments;
use AnyComment\AnyCommentCommentMeta;
use AnyComment\Models\AnyCommentEmailQueue;
use AnyComment\AnyCommentUserMeta;

use AnyComment\Admin\AnyCommentIntegrationSettings;
use AnyComment\Admin\AnyCommentGenericSettings;

class AnyCommentRestComment extends AnyCommentRestController {

	/**
	 * Instance of a comment meta fields object.
	 *
	 * @since 0.2
	 * @var AnyCommentRestCommentMeta
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'anycomment/v1';
		$this->rest_base = 'comments';

		$this->meta = new AnyCommentRestCommentMeta();


		add_action( 'rest_api_init', [ $this, 'register_routes' ] );

		remove_filter( 'comment_text', 'wpautop', 30 );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/count', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_count' ],
				'args'     => [
					'post' => [
						'description' => __( 'Unique post ID', 'anycomment' ),
						'type'        => 'integer',
					],
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'create_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
			'args'   => [
				'id' => [
					'description' => __( 'Unique identifier for the object.', 'anycomment' ),
					'type'        => 'integer',
				],
			],
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args'                => [
					'context'  => $this->get_context_param( [ 'default' => 'view' ] ),
					'password' => [
						'description' => __( 'The password for the parent post of the comment (if the post is password protected).', 'anycomment' ),
						'type'        => 'string',
					],
				],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'update_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/delete/(?P<id>[\d]+)', [
			'args'   => [
				'id' => [
					'description' => __( 'Unique identifier for the object.', 'anycomment' ),
					'type'        => 'integer',
				],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * Checks if a given request has access to read comments.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|bool True if the request has read access, error object otherwise.
	 * @since 4.7.0
	 *
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! empty( $request['post'] ) ) {
			foreach ( (array) $request['post'] as $post_id ) {
				$post = get_post( $post_id );

				if ( ! empty( $post_id ) && $post && ! $this->check_read_post_permission( $post, $request ) ) {
					return new WP_Error( 'rest_cannot_read_post', __( 'Sorry, you are not allowed to read the post for this comment.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
				} elseif ( 0 === $post_id && ! current_user_can( 'moderate_comments' ) ) {
					return new WP_Error( 'rest_cannot_read', __( 'Sorry, you are not allowed to read comments without a post.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
				}
			}
		}

		if ( ! empty( $request['context'] ) && 'edit' === $request['context'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit comments.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			$protected_params = [ 'author', 'author_exclude', 'author_email', 'type', 'status' ];
			$forbidden_params = [];

			foreach ( $protected_params as $param ) {
				if ( 'status' === $param ) {
					if ( 'approve' !== $request[ $param ] ) {
						$forbidden_params[] = $param;
					}
				} elseif ( 'type' === $param ) {
					if ( 'comment' !== $request[ $param ] ) {
						$forbidden_params[] = $param;
					}
				} elseif ( ! empty( $request[ $param ] ) ) {
					$forbidden_params[] = $param;
				}
			}

			if ( ! empty( $forbidden_params ) ) {
				return new WP_Error( 'rest_forbidden_param', sprintf( __( 'Query parameter not permitted: %s', 'anycomment' ), implode( ', ', $forbidden_params ) ), [ 'status' => rest_authorization_required_code() ] );
			}
		}

		return true;
	}

	/**
	 * Get comment count.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 */
	public function get_count( $request ) {

		$post_id = $request->get_param( 'post' );
		$cache   = AnyCommentRestCacheManager::getPostCommentCount( $post_id );

		if ( ! $cache ) {
			return rest_ensure_response( 0 );
		}

		if ( $cache->isHit() ) {
			return rest_ensure_response( $cache->get() );
		}

		$comment_count = 0;

		if ( $post_id !== null ) {
			$data = get_comment_count( $post_id );

			if ( is_array( $data ) ) {
				if ( current_user_can( 'moderate_comments' ) ) {
					$comment_count = $data['all'];
				} else {
					$comment_count = $data['approved'];
				}
			}
		}

		$cache->set( $comment_count )->save();

		return rest_ensure_response( $comment_count );
	}

	/**
	 * Retrieves a list of comment items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function get_items( $request ) {

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal WP_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $registered will be set.
		 */
		$parameter_mappings = array(
			'author'         => 'author__in',
			'author_email'   => 'author_email',
			'author_exclude' => 'author__not_in',
			'exclude'        => 'comment__not_in',
			'include'        => 'comment__in',
			'offset'         => 'offset',
			'order'          => 'order',
			'parent'         => 'parent__in',
			'parent_exclude' => 'parent__not_in',
			'per_page'       => 'number',
			'post'           => 'post__in',
			'search'         => 'search',
			'type'           => 'type',
		);

		$prepared_args = array();

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $prepared_args.
		 */
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$prepared_args[ $wp_param ] = $request[ $api_param ];
			}
		}

		// Ensure certain parameter values default to empty strings.
		foreach ( array( 'author_email', 'search' ) as $param ) {
			if ( ! isset( $prepared_args[ $param ] ) ) {
				$prepared_args[ $param ] = '';
			}
		}

		if ( isset( $registered['orderby'] ) ) {
			$prepared_args['orderby'] = $this->normalize_query_param( $request['orderby'] );
		}

		$prepared_args['no_found_rows'] = false;

		$prepared_args['date_query'] = array();

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $registered['before'], $request['before'] ) ) {
			$prepared_args['date_query'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $registered['after'], $request['after'] ) ) {
			$prepared_args['date_query'][0]['after'] = $request['after'];
		}

		if ( isset( $registered['page'] ) && empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $prepared_args['number'] * ( absint( $request['page'] ) - 1 );
		}

		if ( current_user_can( 'moderate_comments' ) ) {
			$prepared_args['status'] = 'all';
		} else {
			$prepared_args['status'] = 'approve';
		}

		$prepared_args['type'] = [ 'comment', '', 'review' ];

		$query        = new WP_Comment_Query;
		$query_result = $query->query( $prepared_args );

		$comments = array();

		foreach ( $query_result as $comment ) {
			if ( ! $this->check_read_permission( $comment, $request ) ) {
				continue;
			}

			$data = $this->prepare_item_for_response( $comment, $request );

			$comments[] = $this->prepare_response_for_collection( $data );
		}

		$total_comments = (int) $query->found_comments;
		$max_pages      = (int) $query->max_num_pages;

		if ( $total_comments < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $prepared_args['number'], $prepared_args['offset'] );

			$query                  = new WP_Comment_Query;
			$prepared_args['count'] = true;

			$total_comments = $query->query( $prepared_args );

			try {
				$max_pages = ceil( $total_comments / $request['per_page'] );
			} catch ( \Exception $exception ) {
				$max_pages = 0;
			}
		}

		try {
			$current_page = absint( $request['offset'] / $request['per_page'] ) + 1;

			if ( $current_page > $max_pages ) {
				$current_page = $max_pages;
			}
		} catch ( \Exception $exception ) {
			$current_page = 1;
		}

		$response = rest_ensure_response( [
			'items' => $comments,
			'meta'  => [
				'total_count'  => $total_comments,
				'page_count'   => $max_pages,
				'current_page' => $current_page,
				'per_page'     => $request['per_page']
			]
		] );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $request['page'] > 1 ) {
			$prev_page = $request['page'] - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}

		if ( $max_pages > $request['page'] ) {
			$next_page = $request['page'] + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Get the comment, if the ID is valid.
	 *
	 * @param int $id Supplied ID.
	 *
	 * @return WP_Comment|WP_Error Comment object if ID is valid, WP_Error otherwise.
	 * @since 4.7.2
	 *
	 */
	protected function get_comment( $id ) {
		$error = new WP_Error( 'rest_comment_invalid_id', __( 'Invalid comment ID.', 'anycomment' ), array( 'status' => 404 ) );
		if ( (int) $id <= 0 ) {
			return $error;
		}

		$id      = (int) $id;
		$comment = get_comment( $id );
		if ( empty( $comment ) ) {
			return $error;
		}

		if ( ! empty( $comment->comment_post_ID ) ) {
			$post = get_post( (int) $comment->comment_post_ID );
			if ( empty( $post ) ) {
				return new WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.', 'anycomment' ), array( 'status' => 404 ) );
			}
		}

		return $comment;
	}

	/**
	 * Checks if a given request has access to read the comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|bool True if the request has read access for the item, error object otherwise.
	 * @since 4.7.0
	 *
	 */
	public function get_item_permissions_check( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		if ( ! empty( $request['context'] ) && 'edit' === $request['context'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit comments.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$post = get_post( $comment->comment_post_ID );

		if ( ! $this->check_read_permission( $comment, $request ) ) {
			return new WP_Error( 'rest_cannot_read', __( 'Sorry, you are not allowed to read this comment.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( $post && ! $this->check_read_post_permission( $post, $request ) ) {
			return new WP_Error( 'rest_cannot_read_post', __( 'Sorry, you are not allowed to read the post for this comment.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Retrieves a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function get_item( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		$data     = $this->prepare_item_for_response( $comment, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Checks if a given request has access to create a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|bool True if the request has access to create items, error object otherwise.
	 * @since 4.7.0
	 *
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! is_user_logged_in() ) {

			$user = get_user_by( 'email', $request['author_email'] );

			if ( $user instanceof WP_User && AnyCommentUserMeta::is_social_login( $user->ID ) ) {
				return new WP_Error( 'rest_use_social_to_login', __( 'Email was used as social authorization. Please login using this method.', 'anycomment' ), [ 'status' => 403 ] );
			} elseif ( $user instanceof WP_User ) {
				return new WP_Error( 'rest_login_to_leave_comment', __( "User with such email is registered. Please login to leave a comment.", 'anycomment' ), [ 'status' => 403 ] );
			}
		}

		// Limit who can set comment `author`, `author_ip` or `status` to anything other than the default.
		if ( isset( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_comment_invalid_author',
				/* translators: %s: request parameter */
				sprintf( __( "Sorry, you are not allowed to edit '%s' for comments.", 'anycomment' ), 'author' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( isset( $request['author_ip'] ) && ! current_user_can( 'moderate_comments' ) ) {
			if ( empty( $_SERVER['REMOTE_ADDR'] ) || $request['author_ip'] !== $_SERVER['REMOTE_ADDR'] ) {
				return new WP_Error( 'rest_comment_invalid_author_ip',
					/* translators: %s: request parameter */
					sprintf( __( "Sorry, you are not allowed to edit '%s' for comments.", 'anycomment' ), 'author_ip' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}
		}

		if ( isset( $request['status'] ) && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_comment_invalid_status',
				/* translators: %s: request parameter */
				sprintf( __( "Sorry, you are not allowed to edit '%s' for comments.", 'anycomment' ), 'status' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( empty( $request['post'] ) ) {
			return new WP_Error( 'rest_comment_invalid_post_id', __( 'Sorry, you are not allowed to create this comment without a post.', 'anycomment' ), array( 'status' => 403 ) );
		}

		$post = get_post( (int) $request['post'] );
		if ( ! $post ) {
			return new WP_Error( 'rest_comment_invalid_post_id', __( 'Sorry, you are not allowed to create this comment without a post.', 'anycomment' ), array( 'status' => 403 ) );
		}

		if ( 'draft' === $post->post_status ) {
			return new WP_Error( 'rest_comment_draft_post', __( 'Sorry, you are not allowed to create a comment on this post.', 'anycomment' ), array( 'status' => 403 ) );
		}

		if ( 'trash' === $post->post_status ) {
			return new WP_Error( 'rest_comment_trash_post', __( 'Sorry, you are not allowed to create a comment on this post.', 'anycomment' ), array( 'status' => 403 ) );
		}

		if ( ! $this->check_read_post_permission( $post, $request ) ) {
			return new WP_Error( 'rest_cannot_read_post', __( 'Sorry, you are not allowed to read the post for this comment.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! comments_open( $post->ID ) ) {
			return new WP_Error( 'rest_comment_closed', __( 'Sorry, comments are closed for this item.', 'anycomment' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Creates a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function create_item( $request ) {

		if ( ! empty( $request['id'] ) ) {
			return new WP_Error( 'rest_comment_exists', __( 'Cannot create existing comment.', 'anycomment' ), array( 'status' => 400 ) );
		}

		$checkCaptcha = AnyCommentIntegrationSettings::is_recaptcha_active() && (
				( AnyCommentIntegrationSettings::is_recaptcha_user_all() ) ||
				( ! is_user_logged_in() && AnyCommentIntegrationSettings::is_recaptcha_user_guest() ) ||
				( is_user_logged_in() && AnyCommentIntegrationSettings::is_recaptcha_user_auth() )
			);

		if ( $checkCaptcha && ! $this->check_recaptcha( $request['captcha'] ) ) {
			return new WP_Error( 'invalid_captcha', __( 'Captcha is incorrect. Please try again.', 'anycomment' ), [ 'status' => 400 ] );
		}

		if ( ! isset( $request['type'] ) ) {
			$request['type'] = 'comment';
		}

		$prepared_comment = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_comment ) ) {
			return $prepared_comment;
		}

		$prepared_comment['comment_type'] = '';

		/*
		 * Do not allow a comment to be created with missing or empty
		 * comment_content. See wp_handle_comment_submission().
		 */
		if ( $this->is_comment_empty( $prepared_comment['comment_content'] ) ) {
			return new WP_Error( 'rest_comment_content_invalid', __( 'Comment cannot be empty.', 'anycomment' ), [ 'status' => 400 ] );
		}

		// Setting remaining values before wp_insert_comment so we can use wp_allow_comment().
		if ( ! isset( $prepared_comment['comment_date_gmt'] ) ) {
			$prepared_comment['comment_date_gmt'] = current_time( 'mysql', true );
		}

		$should_use_wordpress_login_form = ! is_user_logged_in() && AnyCommentGenericSettings::is_form_type_wordpress();

		if ( $should_use_wordpress_login_form ) {
			return new WP_Error( 'rest_use_wordpress_to_login', __( 'Please login to leave a comment', 'anycomment' ), [ 'status' => 400 ] );
		}

		$should_use_social = ! is_user_logged_in() && AnyCommentGenericSettings::is_form_type_socials();

		if ( ! $should_use_wordpress_login_form && $should_use_social ) {
			return new WP_Error( 'rest_use_social_to_login', __( 'Please use any of the available social networks to leave a comment', 'anycomment' ), [ 'status' => 400 ] );
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			$prepared_comment['user_id']              = $user->ID;
			$prepared_comment['comment_author']       = $user->display_name;
			$prepared_comment['comment_author_email'] = $user->user_email;
			$prepared_comment['comment_author_url']   = $user->user_url;
		} else {


			if ( empty( $prepared_comment['comment_author'] ) ) {
				return new WP_Error( 'rest_comment_author_empty', __( 'Name is required.', 'anycomment' ), [ 'status' => 400 ] );
			}

			if ( AnyCommentGenericSettings::is_guest_field_email_on() && empty( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error( 'rest_comment_email_empty', __( 'Email field is required.', 'anycomment' ), [ 'status' => 400 ] );
			}

			if ( AnyCommentGenericSettings::is_guest_field_email_on() && ! empty( $prepared_comment['comment_author_email'] ) && ! is_email( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error( 'rest_comment_email_invalid', __( 'Provide valid email address.', 'anycomment' ), [ 'status' => 400 ] );
			}
		}

		if ( ! isset( $prepared_comment['comment_author_email'] ) ) {
			$prepared_comment['comment_author_email'] = '';
		}

		if ( ! isset( $prepared_comment['comment_author_url'] ) ) {
			$prepared_comment['comment_author_url'] = '';
		}

		if ( ! isset( $prepared_comment['comment_agent'] ) ) {
			$prepared_comment['comment_agent'] = '';
		}

		$check_comment_lengths = wp_check_comment_data_max_lengths( $prepared_comment );
		if ( is_wp_error( $check_comment_lengths ) ) {
			$error_code = $check_comment_lengths->get_error_code();

			return new WP_Error( $error_code, __( 'Comment field exceeds maximum length allowed.', 'anycomment' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'moderate_comments' ) ) {
			$filter_comment = AnyCommentComments::filter_moderate_words( $prepared_comment['comment_content'] );

			$prepared_comment['comment_content'] = $filter_comment['filtered_text'];
		}


		if ( AnyCommentIntegrationSettings::is_akismet_active() ) {
			apply_filters( 'preprocess_comment', $prepared_comment );
		}

		// Get count before new comment inserted
		$comment_count = AnyCommentUser::get_comment_count_by_user( $prepared_comment['comment_author_email'], true );

		$comment_id = wp_insert_comment( wp_filter_comment( wp_slash( (array) $prepared_comment ) ) );

		if ( ! $comment_id ) {
			return new WP_Error( 'rest_comment_failed_create', __( 'Creating comment failed.', 'anycomment' ), array( 'status' => 500 ) );
		}

		// Process attachments
		if ( ! empty( $request['attachments'] ) ) {
			AnyCommentCommentMeta::add_attachments( $comment_id, $request['attachments'] );
		}

		$should_moderate    = ! current_user_can( 'moderate_comments' ) && AnyCommentGenericSettings::is_moderate_first();
		$has_filtered_words = isset( $filter_comment ) && $filter_comment['match_count'] > 0;
		$has_links          = ! current_user_can( 'moderate_comments' ) && AnyCommentGenericSettings::is_links_on_hold() && AnyCommentComments::has_links( $comment_id );

		if ( AnyCommentGenericSettings::is_moderate_first_comment_only() && $comment_count < 1 ) {
			$should_moderate = true;
		}

		$message = null;

		if ( $should_moderate || $has_filtered_words || $has_links ) {
			$message = __( 'Comment will be shown once reviewed by moderator.', 'anycomment' );
			$this->handle_status_param( 'hold', $comment_id );
		}

		$comment = get_comment( $comment_id );

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $comment_id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$fields_update = $this->update_additional_fields_for_object( $comment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$context = current_user_can( 'moderate_comments' ) ? 'edit' : 'view';

		$request->set_param( 'context', $context );


		$response = rest_ensure_response( [ 'message' => $message ] );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $comment_id ) ) );

		return $response;
	}

	/**
	 * Checks if a given REST request has access to update a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|bool True if the request has access to update the item, error object otherwise.
	 * @since 4.7.0
	 *
	 */
	public function update_item_permissions_check( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		if ( ! $this->check_edit_permission( $comment ) ) {
			return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to edit this comment.', 'anycomment' ), [
				'status' => rest_authorization_required_code(),
			] );
		}

		return true;
	}

	/**
	 * Updates a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function update_item( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		$id = $comment->comment_ID;

		if ( isset( $request['type'] ) && get_comment_type( $id ) !== $request['type'] ) {
			return new WP_Error( 'rest_comment_invalid_type', __( 'Sorry, you are not allowed to change the comment type.', 'anycomment' ), [ 'status' => 404 ] );
		}

		$prepared_args = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_args ) ) {
			return $prepared_args;
		}

		if ( ! empty( $prepared_args['comment_post_ID'] ) ) {
			$post = get_post( $prepared_args['comment_post_ID'] );
			if ( empty( $post ) ) {
				return new WP_Error( 'rest_comment_invalid_post_id', __( 'Invalid post ID.', 'anycomment' ), array( 'status' => 403 ) );
			}
		}

		if ( empty( $prepared_args ) && isset( $request['status'] ) ) {
			// Only the comment status is being changed.
			$change = $this->handle_status_param( $request['status'], $id );

			if ( ! $change ) {
				return new WP_Error( 'rest_comment_failed_edit', __( 'Updating comment status failed.', 'anycomment' ), array( 'status' => 500 ) );
			}
		} elseif ( ! empty( $prepared_args ) ) {
			if ( is_wp_error( $prepared_args ) ) {
				return $prepared_args;
			}

			if ( $this->is_comment_empty( $prepared_args['comment_content'] ) ) {
				return new WP_Error( 'rest_comment_content_invalid', __( 'Comment cannot be empty.', 'anycomment' ), [ 'status' => 400 ] );
			}

			$prepared_args['comment_ID'] = $id;

			$check_comment_lengths = wp_check_comment_data_max_lengths( $prepared_args );
			if ( is_wp_error( $check_comment_lengths ) ) {
				$error_code = $check_comment_lengths->get_error_code();

				return new WP_Error( $error_code, __( 'Comment field exceeds maximum length allowed.', 'anycomment' ), array( 'status' => 400 ) );
			}

			$updated = wp_update_comment( wp_slash( (array) $prepared_args ) );

			if ( false === $updated ) {
				return new WP_Error( 'rest_comment_failed_edit', __( 'Updating comment failed.', 'anycomment' ), array( 'status' => 500 ) );
			}

			if ( isset( $request['status'] ) ) {
				$this->handle_status_param( $request['status'], $id );
			}
		}

		// Process attachments
		if ( ! empty( $request['attachments'] ) ) {
			// Would add non existing ones, add keep old ones
			AnyCommentCommentMeta::add_attachments( $id, $request['attachments'] );
		}

		$comment = get_comment( $id );

		$fields_update = $this->update_additional_fields_for_object( $comment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $comment, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to delete a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|bool True if the request has access to delete the item, error object otherwise.
	 * @since 4.7.0
	 *
	 */
	public function delete_item_permissions_check( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		if ( ! $this->check_edit_permission( $comment ) ) {
			return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this comment.', 'anycomment' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Deletes a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function delete_item( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		/**
		 * Filters whether a comment can be trashed.
		 *
		 * Return false to disable trash support for the post.
		 *
		 * @param bool $supports_trash Whether the post type support trashing.
		 * @param WP_Post $comment The comment object being considered for trashing support.
		 *
		 * @since 4.7.0
		 *
		 */
		$supports_trash = apply_filters( 'rest_comment_trashable', ( EMPTY_TRASH_DAYS > 0 ), $comment );

		$request->set_param( 'context', 'edit' );

		// If this type doesn't support trashing, error out.
		if ( ! $supports_trash ) {
			/* translators: %s: force=true */
			return new WP_Error( 'rest_trash_not_supported', sprintf( __( "The comment does not support trashing. Set '%s' to delete.", 'anycomment' ), 'force=true' ), array( 'status' => 501 ) );
		}

		if ( 'trash' === $comment->comment_approved ) {
			return new WP_Error( 'rest_already_trashed', __( 'The comment has already been trashed.', 'anycomment' ), array( 'status' => 410 ) );
		}

		$result   = wp_trash_comment( $comment->comment_ID );
		$comment  = get_comment( $comment->comment_ID );
		$response = $this->prepare_item_for_response( $comment, $request );


		if ( ! $result ) {
			return new WP_Error( 'rest_cannot_delete', __( 'The comment cannot be deleted.', 'anycomment' ), array( 'status' => 500 ) );
		}

		return $response;
	}

	/**
	 * Prepares a single comment output for response.
	 *
	 * @param WP_Comment $comment Comment object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 * @since 4.7.0
	 *
	 */
	public function prepare_item_for_response( $comment, $request ) {


		$prepared_args['parent']  = $comment->comment_ID;
		$prepared_args['post_id'] = $comment->comment_post_ID;

		if ( current_user_can( 'moderate_comments' ) ) {
			$prepared_args['status'] = 'all';
		} else {
			$prepared_args['status'] = 'approve';
		}

		if ( isset( $request['order'] ) ) {
			$prepared_args['order'] = $request['order'];
		}

		if ( isset( $request['orderby'] ) ) {
			$prepared_args['orderby'] = $this->normalize_query_param( $request['orderby'] );
		}

		$query          = new WP_Comment_Query;
		$child_comments = $query->query( $prepared_args );

		if ( ! empty( $child_comments ) ) {

			foreach ( $child_comments as $key => $child_comment ) {
				$prepared_child_comment = $this->prepare_item_for_response( $child_comment, $request );

				if ( isset( $prepared_child_comment->data ) ) {
					$prepared_child_comment = $prepared_child_comment->data;
				}
				$child_comments[ $key ] = $prepared_child_comment;
			}
		} else {
			$child_comments = null;
		}

		$is_post_author = false;

		if ( ( $post = get_post( $comment->comment_post_ID ) ) !== null ) {
			$is_post_author = (int) $post->post_author === (int) $comment->user_id;
		}

		if ( AnyCommentGenericSettings::is_show_profile_url() && ( $socialUrl = AnyCommentUserMeta::get_social_profile_url( $comment->user_id ) ) !== null ) {
			$profileUrl = $socialUrl;
		} elseif ( ! empty( $comment->comment_author_url ) ) {
			$profileUrl = $comment->comment_author_url;
		} elseif ( ! empty( $comment->user_id ) && false !== ( $user = get_user_by( 'id', $comment->user_id ) ) && ! empty( $user->user_url ) ) {
			$profileUrl = $user->user_url;
		} else {
			$profileUrl = '';
		}

		$owner = [
			'id'              => $comment->user_id,
			'is_post_author'  => $is_post_author,
			'is_social_login' => AnyCommentUserMeta::is_social_login( $comment->user_id ),
			'social_type'     => AnyCommentUserMeta::get_social_type( $comment->user_id ),
			'profile_url'     => $profileUrl,
		];

		$native_date = mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $comment->comment_date );

		$data = array(
			'id'                 => (int) $comment->comment_ID,
			'post'               => (int) $comment->comment_post_ID,
			'parent'             => (int) $comment->comment_parent,
			'parent_author_name' => (int) $comment->comment_parent !== 0 ? get_comment_author( $comment->comment_parent ) : '',
			'author'             => (int) $comment->user_id,
			'author_name'        => $comment->comment_author,
			'date'               => gmdate( 'c', strtotime( $comment->comment_date_gmt ) ),
			'date_gmt'           => mysql2date( 'c', $comment->comment_date_gmt ),
			'date_native'        => $native_date,
			'content'            => $comment->comment_content,
			'avatar_url'         => AnyCommentSocialAuth::get_user_avatar_url( (int) $comment->user_id !== 0 ? $comment->user_id : $comment->comment_author_email ),
			'children'           => $child_comments,
			'owner'              => $owner,
			'attachments'        => AnyCommentCommentMeta::get_attachments_for_api( $comment->comment_ID ),
			'permissions'        => [
				'can_edit_comment' => AnyCommentUser::can_edit_comment( $comment ),
			],
			'meta'               => [
				'has_like'    => AnyCommentLikes::is_user_has_like( $comment->comment_ID ),
				'has_dislike' => AnyCommentLikes::is_user_has_dislike( $comment->comment_ID ),
				'status'      => wp_get_comment_status( $comment ),
				'is_updated'  => AnyCommentCommentMeta::is_updated( $comment ),
				'updated_by'  => AnyCommentCommentMeta::get_updated_by( $comment ),
			],
		);

		$data['meta'] = array_merge( $data['meta'], (array) AnyCommentLikes::get_summary( $comment->comment_ID ) );

		/**
		 * Filters comment data.
		 *
		 * @param array $data Comment data, see structure above.
		 *
		 * @since 0.0.79
		 *
		 * @package WP_Comment $comment Comment object.
		 */
		$data = apply_filters( 'anycomment/rest/comments/item_for_response', $data, $comment );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepends internal property prefix to query parameters to match our response fields.
	 *
	 * @param string $query_param Query parameter.
	 *
	 * @return string The normalized query parameter.
	 */
	protected function normalize_query_param( $query_param ) {
		$prefix = 'comment_';

		switch ( $query_param ) {
			case 'id':
				$normalized = $prefix . 'ID';
				break;
			case 'post':
				$normalized = $prefix . 'post_ID';
				break;
			case 'parent':
				$normalized = $prefix . 'parent';
				break;
			case 'include':
				$normalized = 'comment__in';
				break;
			default:
				$normalized = $prefix . $query_param;
				break;
		}

		return $normalized;
	}

	/**
	 * Checks comment_approved to set comment status for single comment output.
	 *
	 *
	 * @param string|int $comment_approved comment status.
	 *
	 * @return string Comment status.
	 */
	protected function prepare_status_response( $comment_approved ) {

		switch ( $comment_approved ) {
			case 'hold':
			case '0':
				$status = 'hold';
				break;

			case 'approve':
			case '1':
				$status = 'approved';
				break;

			case 'spam':
			case 'trash':
			default:
				$status = $comment_approved;
				break;
		}

		return $status;
	}

	/**
	 * Prepares a single comment to be inserted into the database.
	 *
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array|WP_Error Prepared comment, otherwise WP_Error object.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_comment = array();

		/*
		 * Allow the comment_content to be set via the 'content' or
		 * the 'content.raw' properties of the Request object.
		 */
		if ( isset( $request['content'] ) && is_string( $request['content'] ) ) {
			$prepared_comment['comment_content'] = $request['content'];
		} elseif ( isset( $request['content']['raw'] ) && is_string( $request['content']['raw'] ) ) {
			$prepared_comment['comment_content'] = $request['content']['raw'];
		}

		if ( isset( $request['post'] ) ) {
			$prepared_comment['comment_post_ID'] = (int) $request['post'];
		}

		if ( isset( $request['parent'] ) ) {
			$prepared_comment['comment_parent'] = $request['parent'];
		}

		if ( isset( $request['author'] ) ) {
			$user = new WP_User( $request['author'] );

			if ( $user->exists() ) {
				$prepared_comment['user_id']              = $user->ID;
				$prepared_comment['comment_author']       = $user->display_name;
				$prepared_comment['comment_author_email'] = $user->user_email;
				$prepared_comment['comment_author_url']   = $user->user_url;
			} else {
				return new WP_Error( 'rest_comment_author_invalid', __( 'Invalid comment author ID.', 'anycomment' ), [ 'status' => 400 ] );
			}
		}

		if ( isset( $request['author_name'] ) ) {
			$prepared_comment['comment_author'] = $request['author_name'];
		}

		if ( isset( $request['author_email'] ) ) {
			$prepared_comment['comment_author_email'] = $request['author_email'];
		}

		if ( isset( $request['author_url'] ) ) {
			$prepared_comment['comment_author_url'] = $request['author_url'];
		}

		if ( isset( $request['author_ip'] ) && current_user_can( 'moderate_comments' ) ) {
			$prepared_comment['comment_author_IP'] = $request['author_ip'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) && rest_is_ip_address( $_SERVER['REMOTE_ADDR'] ) ) {
			$prepared_comment['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
		} else {
			$prepared_comment['comment_author_IP'] = '127.0.0.1';
		}

		if ( $request->get_header( 'user_agent' ) ) {
			$prepared_comment['comment_agent'] = $request->get_header( 'user_agent' );
		}

		return $prepared_comment;
	}

	/**
	 * Retrieves the comment's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'comment',
			'type'       => 'object',
			'properties' => array(
				'id'                => array(
					'description' => __( 'Unique identifier for the object.', 'anycomment' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'author'            => array(
					'description' => __( 'The ID of the user object, if author was a user.', 'anycomment' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'author_email'      => array(
					'description' => __( 'Email address for the object author.', 'anycomment' ),
					'type'        => 'string',
					'format'      => 'email',
					'context'     => array( 'edit' ),
				),
				'author_ip'         => array(
					'description' => __( 'IP address for the object author.', 'anycomment' ),
					'type'        => 'string',
					'format'      => 'ip',
					'context'     => array( 'edit' ),
				),
				'author_name'       => array(
					'description' => __( 'Display name for the object author.', 'anycomment' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'author_url'        => array(
					'description' => __( 'URL for the object author.', 'anycomment' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'author_user_agent' => array(
					'description' => __( 'User agent for the object author.', 'anycomment' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'content'           => array(
					'description' => __( 'The content for the object.', 'anycomment' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => null,
						// Note: sanitization implemented in self::prepare_item_for_database()
						'validate_callback' => null,
						// Note: validation implemented in self::prepare_item_for_database()
					),
					'properties'  => array(
						'raw'      => array(
							'description' => __( 'Content for the object, as it exists in the database.', 'anycomment' ),
							'type'        => 'string',
							'context'     => array( 'edit' ),
						),
						'rendered' => array(
							'description' => __( 'HTML content for the object, transformed for display.', 'anycomment' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					),
				),
				'date'              => array(
					'description' => __( "The date the object was published, in the site's timezone.", 'anycomment' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date_gmt'          => array(
					'description' => __( 'The date the object was published, as GMT.', 'anycomment' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'link'              => array(
					'description' => __( 'URL to the object.', 'anycomment' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'parent'            => array(
					'description' => __( 'The ID for the parent of the object.', 'anycomment' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'default'     => 0,
				),
				'post'              => array(
					'description' => __( 'The ID of the associated post object.', 'anycomment' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'default'     => 0,
				),
				'status'            => array(
					'description' => __( 'State of the object.', 'anycomment' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_key',
					),
				),
				'type'              => array(
					'description' => __( 'Type of Comment for the object.', 'anycomment' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		if ( get_option( 'show_avatars' ) ) {
			$avatar_properties = array();

			$avatar_sizes = rest_get_avatar_sizes();
			foreach ( $avatar_sizes as $size ) {
				$avatar_properties[ $size ] = array(
					/* translators: %d: avatar image size in pixels */
					'description' => sprintf( __( 'Avatar URL with image size of %d pixels.', 'anycomment' ), $size ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
				);
			}

			$schema['properties']['author_avatar_urls'] = array(
				'description' => __( 'Avatar URLs for the object author.', 'anycomment' ),
				'type'        => 'object',
				'context'     => array( 'view', 'edit', 'embed' ),
				'readonly'    => true,
				'properties'  => $avatar_properties,
			);
		}

		$schema['properties']['meta'] = $this->meta->get_field_schema();

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 *
	 * @return array Comments collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params['after'] = array(
			'description' => __( 'Limit response to comments published after a given ISO8601 compliant date.', 'anycomment' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		$query_params['author'] = array(
			'description' => __( 'Limit result set to comments assigned to specific user IDs. Requires authorization.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
		);

		$query_params['author_exclude'] = array(
			'description' => __( 'Ensure result set excludes comments assigned to specific user IDs. Requires authorization.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
		);

		$query_params['author_email'] = array(
			'default'     => null,
			'description' => __( 'Limit result set to that from a specific author email. Requires authorization.', 'anycomment' ),
			'format'      => 'email',
			'type'        => 'string',
		);

		$query_params['before'] = array(
			'description' => __( 'Limit response to comments published before a given ISO8601 compliant date.', 'anycomment' ),
			'type'        => 'string',
			'format'      => 'date-time',
		);

		$query_params['exclude'] = array(
			'description' => __( 'Ensure result set excludes specific IDs.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['include'] = array(
			'description' => __( 'Limit result set to specific IDs.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['offset'] = array(
			'description' => __( 'Offset the result set by a specific number of items.', 'anycomment' ),
			'type'        => 'integer',
		);

		$query_params['order'] = array(
			'description' => __( 'Order sort attribute ascending or descending.', 'anycomment' ),
			'type'        => 'string',
			'default'     => 'desc',
			'enum'        => array(
				'asc',
				'desc',
			),
		);

		$query_params['orderby'] = array(
			'description' => __( 'Sort collection by object attribute.', 'anycomment' ),
			'type'        => 'string',
			'default'     => 'date_gmt',
			'enum'        => array(
				'date',
				'date_gmt',
				'id',
				'include',
				'post',
				'parent',
				'type',
			),
		);

		$query_params['parent'] = array(
			'default'     => array(),
			'description' => __( 'Limit result set to comments of specific parent IDs.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
		);

		$query_params['parent_exclude'] = array(
			'default'     => array(),
			'description' => __( 'Ensure result set excludes specific parent IDs.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
		);

		$query_params['post'] = array(
			'default'     => array(),
			'description' => __( 'Limit result set to comments assigned to specific post IDs.', 'anycomment' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
		);

		$query_params['status'] = array(
			'default'           => 'approve',
			'description'       => __( 'Limit result set to comments assigned a specific status. Requires authorization.', 'anycomment' ),
			'sanitize_callback' => 'sanitize_key',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$query_params['type'] = array(
			'default'           => 'comment',
			'description'       => __( 'Limit result set to comments assigned a specific type. Requires authorization.', 'anycomment' ),
			'sanitize_callback' => 'sanitize_key',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$query_params['password'] = array(
			'description' => __( 'The password for the post if it is password protected.', 'anycomment' ),
			'type'        => 'string',
		);

		/**
		 * Filter collection parameters for the comments controller.
		 *
		 * This filter registers the collection parameter, but does not map the
		 * collection parameter to an internal WP_Comment_Query parameter. Use the
		 * `rest_comment_query` filter to set WP_Comment_Query parameters.
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_comment_collection_params', $query_params );
	}

	/**
	 * Sets the comment_status of a given comment object when creating or updating a comment.
	 *
	 * @param string|int $new_status New comment status.
	 * @param int $comment_id Comment ID.
	 *
	 * @return bool Whether the status was changed.
	 */
	protected function handle_status_param( $new_status, $comment_id ) {
		$old_status = wp_get_comment_status( $comment_id );

		if ( $new_status === $old_status ) {
			return false;
		}

		switch ( $new_status ) {
			case 'approved' :
			case 'approve':
			case '1':
				$changed = wp_set_comment_status( $comment_id, 'approve' );
				break;
			case 'hold':
			case '0':
				$changed = wp_set_comment_status( $comment_id, 'hold' );
				break;
			case 'spam' :
				$changed = wp_spam_comment( $comment_id );
				break;
			case 'unspam' :
				$changed = wp_unspam_comment( $comment_id );
				break;
			case 'trash' :
				$changed = wp_trash_comment( $comment_id );
				break;
			case 'untrash' :
				$changed = wp_untrash_comment( $comment_id );
				break;
			default :
				$changed = false;
				break;
		}

		return $changed;
	}

	/**
	 * Checks if the post can be read.
	 *
	 * Correctly handles posts with the inherit status.
	 *
	 * @param WP_Post $post Post object.
	 * @param WP_REST_Request $request Request data to check.
	 *
	 * @return bool Whether post can be read.
	 */
	protected function check_read_post_permission( $post, $request ) {
		$posts_controller = new WP_REST_Posts_Controller( $post->post_type );
		$post_type        = get_post_type_object( $post->post_type );

		$has_password_filter = false;

		// Only check password if a specific post was queried for or a single comment
		$requested_post    = ! empty( $request['post'] ) && ( ! is_array( $request['post'] ) || 1 === count( $request['post'] ) );
		$requested_comment = ! empty( $request['id'] );
		if ( ( $requested_post || $requested_comment ) && $posts_controller->can_access_password_content( $post, $request ) ) {
			add_filter( 'post_password_required', '__return_false' );

			$has_password_filter = true;
		}

		$result = true;

		if ( post_password_required( $post ) ) {
			$result = current_user_can( $post_type->cap->edit_post, $post->ID );
		}

		if ( $has_password_filter ) {
			remove_filter( 'post_password_required', '__return_false' );
		}

		return $result;
	}

	/**
	 * Checks if the comment can be read.
	 *
	 * @param WP_Comment $comment Comment object.
	 * @param WP_REST_Request $request Request data to check.
	 *
	 * @return bool Whether the comment can be read.
	 */
	protected function check_read_permission( $comment, $request ) {
		if ( ! empty( $comment->comment_post_ID ) ) {
			$post = get_post( $comment->comment_post_ID );
			if ( $post ) {
				if ( $this->check_read_post_permission( $post, $request ) && 1 === (int) $comment->comment_approved ) {
					return true;
				}
			}
		}

		if ( 0 === get_current_user_id() ) {
			return false;
		}

		if ( empty( $comment->comment_post_ID ) && ! current_user_can( 'moderate_comments' ) ) {
			return false;
		}

		if ( ! empty( $comment->user_id ) && get_current_user_id() === (int) $comment->user_id ) {
			return true;
		}

		return current_user_can( 'edit_comment', $comment->comment_ID );
	}

	/**
	 * Checks if a comment can be edited or deleted.
	 *
	 * @param WP_Comment $comment Comment object.
	 *
	 * @return bool Whether the comment can be edited or deleted.
	 */
	protected function check_edit_permission( $comment ) {

		if ( 0 === (int) get_current_user_id() ) {
			return false;
		}

		if ( current_user_can( 'moderate_comments' ) ||
		     current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return true;
		}

		if ( $this->is_old_to_edit( $comment ) ) {
			return false;
		}

		$user = wp_get_current_user();

		if ( ! $user instanceof WP_User ) {
			return false;
		}

		return (int) $comment->user_id === (int) $user->ID;
	}

	/**
	 * Check whether comment field is empty or not.
	 *
	 * @param string $comment_text Comment text to be tested.
	 *
	 * @return bool
	 */
	public function is_comment_empty( $comment_text ) {
		$comment_text = trim( $comment_text );

		if ( empty( $comment_text ) ) {
			return true;
		}

		/**
		 * @link https://regex101.com/r/tlvIcV/1
		 */
		$empty_regex = '/^<p>(<br>|<br\/>|<br\s\/>|\s+|)<\/p>$/m';

		return preg_match( $empty_regex, $comment_text );
	}

	/**
	 * Check whether it is too old to edit (update/delete) comment.
	 *
	 * @param WP_Comment $comment Comment to be checked.
	 * @param int $minutes Number of minutes comment allow to be edited.
	 *
	 * Note: if `$minutes` is below 5, it will be set to 5 as it is the default value.
	 *
	 * @return bool
	 */
	public function is_old_to_edit( $comment, $minutes = 5 ) {
		return AnyCommentUser::is_old_to_edit( $comment, $minutes );
	}

	/**
	 * Checks a comment author email for validity.
	 *
	 * Accepts either a valid email address or empty string as a valid comment
	 * author email address. Setting the comment author email to an empty
	 * string is allowed when a comment is being updated.
	 *
	 * @param string $value Author email value submitted.
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string $param The parameter name.
	 *
	 * @return WP_Error|string The sanitized email address, if valid,
	 *                         otherwise an error.
	 */
	public function check_comment_author_email( $value, $request, $param ) {
		$email = (string) $value;
		if ( empty( $email ) ) {
			return $email;
		}

		$check_email = rest_validate_request_arg( $email, $request, $param );
		if ( is_wp_error( $check_email ) ) {
			return $check_email;
		}

		return $email;
	}

	/**
	 * Check whether provided reCaptcha token is valid.
	 *
	 * @param $token
	 *
	 * @return bool|WP_Error
	 */
	public function check_recaptcha( $token ) {
		$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
			'body' => [
				'secret'   => AnyCommentIntegrationSettings::get_recaptcha_site_secret(),
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '',
			],
		] );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		if ( isset( $response['response']['code'] ) && (int) $response['response']['code'] !== 200 ) {
			return false;
		}

		$body = json_decode( $response['body'], true );

		if ( isset( $body['success'] ) && (bool) $body['success'] ) {
			return true;
		}


		return new WP_Error( 403, implode( ',', $body['error-codes'] ) );
	}
}
