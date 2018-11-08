<?php

namespace AnyComment\Rest;

use WP_Error;
use WP_User;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

use AnyComment\Models\AnyCommentLikes;

class AnyCommentRestLikes extends AnyCommentRestController {

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'anycomment/v1';
		$this->rest_base = 'likes';

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, [
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
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_comment_like_login_required', __( 'Login to like comment', 'anycomment' ), [ 'status' => 403 ] );
		}

		if ( empty( $request['post'] ) ) {
			return new WP_Error( 'rest_comment_like_invalid_post_id', __( 'Sorry, post does not exist.', 'anycomment' ), array( 'status' => 403 ) );
		}

		if ( empty( $request['comment'] ) ) {
			return new WP_Error( 'rest_comment_like_invalid_post_id', __( 'Sorry, comment that you liked does not exist.', 'anycomment' ), array( 'status' => 403 ) );
		}

		$commentId = (int) $request['comment'];

		$comment = get_comment( $commentId );

		if ( ! $comment ) {
			return new WP_Error( 'rest_comment_like_invalid_post_id', __( 'Sorry, comment that you liked does not exist.', 'anycomment' ), array( 'status' => 403 ) );
		}

		$post = get_post( (int) $request['post'] );

		if ( ! $post ) {
			return new WP_Error( 'rest_comment_like_invalid_post_id', __( 'Sorry, post does not exist.', 'anycomment' ), array( 'status' => 403 ) );
		}

		if ( 'draft' === $post->post_status ) {
			return new WP_Error( 'rest_comment_like_draft_post', __( 'Sorry, you are not allowed to create a comment on this post.', 'anycomment' ), array( 'status' => 403 ) );
		}

		if ( 'trash' === $post->post_status ) {
			return new WP_Error( 'rest_comment_like_trash_post', __( 'Sorry, you are not allowed to create a comment on this post.', 'anycomment' ), array( 'status' => 403 ) );
		}

		return true;
	}


	/**
	 * Creates a comment.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 */
	public function create_item( $request ) {

		if ( ! empty( $request['id'] ) ) {
			return new WP_Error( 'rest_comment_like_exists', __( 'Cannot create existing like.', 'anycomment' ), array( 'status' => 400 ) );
		}

		$prepareLike = new AnyCommentLikes();

		$prepareLike->comment_ID = $request['comment'];

		if ( ( $user = wp_get_current_user() ) instanceof WP_User ) {
			$prepareLike->user_ID = $user->id;
		}

		$prepareLike->post_ID = $request['post'];

		if ( ! AnyCommentLikes::isCurrentUserHasLike( $prepareLike->comment_ID, $user->ID ) ) {
			$like = AnyCommentLikes::addLike( $prepareLike );
			if ( ! $like ) {
				return new WP_Error( 'rest_like_fail', __( 'Failed to like', 'anycomment' ), [ 'status' => 400 ] );
			}
		} else {
			AnyCommentLikes::deleteLike( $prepareLike->comment_ID );
			$like = $prepareLike;
		}

		$response = $this->prepare_item_for_response( $like, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $like->ID ) ) );


		return $response;
	}

	/**
	 * Prepares a single like output for response.
	 *
	 * @since 4.7.0
	 *
	 * @param AnyCommentLikes $like Like object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $like, $request ) {

		$data = [];

		if ( (int) $like->ID != 0 ) {
			$data = [
				'liked_at' => mysql_to_rfc3339( $like->liked_at )
			];
		}

		$data['total_count'] = AnyCommentLikes::get_likes_count( $like->comment_ID );
		$data['has_like']    = AnyCommentLikes::isCurrentUserHasLike( $like->comment_ID );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

}