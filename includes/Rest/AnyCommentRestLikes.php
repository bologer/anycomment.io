<?php

namespace AnyComment\Rest;

use AnyComment\AnyCommentMutex;
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
				'args'                => [
					'comment' => [
						'description' => __( 'Comment id.', 'anycomment' ),
						'type'        => 'integer',
					],
					'post'    => [
						'description' => __( 'Post id.', 'anycomment' ),
						'type'        => 'integer',
					],
					'type'    => [
						'description' => sprintf( __( 'Like - %d or dislike - %d.', 'anycomment' ), AnyCommentLikes::TYPE_LIKE, AnyCommentLikes::TYPE_DISLIKE ),
						'type'        => 'integer',
					],
				],
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

		$type = $request['type'];

		if ( $type !== AnyCommentLikes::TYPE_LIKE && $type !== AnyCommentLikes::TYPE_DISLIKE ) {
			return new WP_Error( 'rest_comment_rating_invalid', sprintf( __( 'Sorry, rating type can be only %s or %s.', 'anycomment' ), AnyCommentLikes::TYPE_LIKE, AnyCommentLikes::TYPE_DISLIKE ), array( 'status' => 403 ) );
		}

		return true;
	}


	/**
	 * Creates a comment.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
	 */
	public function create_item( $request ) {

		if ( ! empty( $request['id'] ) ) {
			return new WP_Error( 'rest_comment_like_exists', __( 'Cannot create existing like.', 'anycomment' ), array( 'status' => 400 ) );
		}


		try {
			$lockName = sprintf( 'like-dislike-lock-%s-%s-%d', $request['post'], $request['type'], get_current_user_id() );
			if ( ! AnyCommentMutex::acquire( $lockName ) ) {
				return new WP_Error( 'rest_like_fail', __( 'Failed to like', 'anycomment' ), [ 'status' => 400 ] );
			}

			$model             = new AnyCommentLikes();
			$model->comment_ID = $request['comment'];
			$user_id           = (int) get_current_user_id();
			$model->user_ID    = $user_id !== 0 ? $user_id : null;
			$model->post_ID    = $request['post'];
			$model->type       = $request['type'];

			if ( ! AnyCommentLikes::is_user_has( $model->type, $model->comment_ID ) ) {

				// Should delete opposite of type to make sure user does not
				// set like and dislike at the same time
				if ( $model->type === AnyCommentLikes::TYPE_LIKE ) {
					AnyCommentLikes::delete_dislike( $model->comment_ID );
				} else {
					AnyCommentLikes::delete_like( $model->comment_ID );
				}

				if ( ! $model->save() ) {
					return new WP_Error(
						'rest_like_fail',
						sprintf(
							__( 'Failed to %s', 'anycomment' ),
							( $model->type === AnyCommentLikes::TYPE_LIKE ? __( 'like', 'anycomment' ) : __( 'dislike', 'anycomment' ) )
						),
						[ 'status' => 400 ]
					);
				}
			} else {
				AnyCommentLikes::delete_by_type( $model->type, $model->comment_ID );
			}

			$response = $this->prepare_item_for_response( $model, $request );
			$response = rest_ensure_response( $response );

			$response->set_status( 201 );
			$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $model->ID ) ) );
		} catch ( \Exception $e ) {
			$response->set_status( 400 );
		} finally {
			AnyCommentMutex::release( $lockName );
		}

		return $response;
	}

	/**
	 * Prepares a single like output for response.
	 *
	 * @param AnyCommentLikes $model Like object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $model, $request ) {

		$data = [];

		if ( (int) $model->ID != 0 ) {
			$data = [
				'liked_at' => mysql_to_rfc3339( $model->liked_at )
			];
		}

		$data['has_like']    = AnyCommentLikes::is_user_has_like( $model->comment_ID );
		$data['has_dislike'] = AnyCommentLikes::is_user_has_dislike( $model->comment_ID );

		$data = array_merge( $data, (array) AnyCommentLikes::get_summary( $model->comment_ID ) );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

}
