<?php

namespace AnyComment\Rest;

use AnyComment\Action\Comment\ListComments;
use AnyComment\AnyCommentCore;
use AnyComment\AnyCommentUser;
use AnyComment\Cache\AnyCommentRestCacheManager;
use AnyComment\Domain\Comment\CommentService;
use AnyComment\Domain\Comment\CreateComment;
use WP_Post;
use WP_Error;
use WP_Comment;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;
use AnyComment\AnyCommentCommentMeta;

class CommentController extends AnyCommentRestController {

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
		$this->rest_base = 'comment';

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
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_items' ],
				'args'     => $this->get_collection_params(),
			],
			[
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'create_item' ],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
			'args' => [
				'id' => [
					'description' => __( 'Unique identifier for the object.', 'anycomment' ),
					'type'        => 'integer',
				],
			],
			[ 'methods' => WP_REST_Server::READABLE, 'callback' => [ $this, 'get_item' ] ],
			[ 'methods' => WP_REST_Server::EDITABLE, 'callback' => [ $this, 'update_item' ] ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/delete/(?P<id>[\d]+)', [
			[
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'delete_item' ],
			],
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
		/** @var ListComments $action */
		$action = AnyCommentCore::getContainer()->get( ListComments::class );
		$data   = $action->action(
			$request->get_param( 'url' ),
			$request->get_param( 'page' ),
			$request->get_param( 'per-page' )
		);

		return new WP_REST_Response( $data, 200, [] );
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
	 * Creates a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function create_item( $request ) {

		/** @var CommentService $service */
		$service = AnyCommentCore::getContainer()->get( CommentService::class );

		$postId = url_to_postid( $request->get_param( 'url' ) );

		$service->create( new CreateComment(
			1,
			$postId,
			$request->get_param( 'content' ),
			$request->get_param( 'parent_id' )
		) );

		$data = [
			'status'   => "ok",
			'response' => [
				"content"           => "<p>Test</p>",
				"parent_id"         => null,
				"author_id"         => 422210,
				"website_id"        => 1,
				"website_page_id"   => 1,
				"website_page_hash" => "38cd44dab6d84d14ca89487eae8c32df",
				"ip"                => "86.49.254.231",
				"status"            => 0,
				"meta"              => [ "attachments" => [] ],
				"like_count"        => 0,
				"dislike_count"     => 0,
				"created_date"      => "2021-06-27 15=>01:07+00",
				"updated_date"      => "2021-06-27 15:01:07+00",
				"id"                => 1128167
			]
		];

		$response = new WP_REST_Response( $data, 201 );

		return $response;
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
}
