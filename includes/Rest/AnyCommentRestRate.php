<?php

namespace AnyComment\Rest;

use WP_Error;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;

use AnyComment\Helpers\AnyCommentRequest;
use AnyComment\Models\AnyCommentRating;
use AnyComment\Admin\AnyCommentGenericSettings;


class AnyCommentRestRate extends AnyCommentRestController {
	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'anycomment/v1';
		$this->rest_base = 'rate';

		if ( AnyCommentGenericSettings::is_rating_on() ) {
			add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		}
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
			'args'   => [
				'rating' => [
					'description' => __( "Rating from 1-5.", 'anycomment' ),
					'type'        => 'int'
				],
				'post'   => [
					'description' => __( "Post ID", 'anycomment' ),
					'type'        => 'int'
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_item_permissions_check( $request ) {

		if ( empty( $request['post'] ) ) {
			return new WP_Error( 'rest_rate_post_required', __( 'Sorry, post is required.', 'anycomment' ), [ 'status' => 403 ] );
		}

		$rating = isset( $request['rating'] ) ? $request['rating'] : 0;

		if ( empty( $rating ) ) {
			return new WP_Error( 'rest_rate_rating_required', __( 'Sorry, rating is required.', 'anycomment' ), [ 'status' => 403 ] );
		}

		if ( $rating < 1 || $rating > 5 ) {
			return new WP_Error( 'rest_rate_rating_incorrect_value', __( 'Sorry, rating should be between 1 and 5.' ), [ 'status' => 403 ] );
		}

		$post = get_post( (int) $request['post'] );

		if ( ! $post ) {
			return new WP_Error( 'rest_rate_invalid_post_id', __( 'Sorry, post does not exist.', 'anycomment' ), [ 'status' => 403 ] );
		}

		if ( 'draft' === $post->post_status ) {
			return new WP_Error( 'rest_rate_draft_post', __( 'Sorry, you are not allowed to create a comment on this post.', 'anycomment' ), [ 'status' => 403 ] );
		}

		if ( 'trash' === $post->post_status ) {
			return new WP_Error( 'rest_rate_trash_post', __( 'Sorry, you are not allowed to create a comment on this post.', 'anycomment' ), [ 'status' => 403 ] );
		}

		$user          = wp_get_current_user();
		$user_id_or_ip = null;

		if ( (int) $user->ID !== 0 ) {
			$user_id_or_ip = $user->ID;
		} else {
			$user_id_or_ip = AnyCommentRequest::get_user_ip();
		}

		if ( AnyCommentRating::current_user_rated( $post->ID, $user_id_or_ip ) ) {
			return new WP_Error( 'rest_rate_already_rated', __( 'You have already rated', 'anycomment' ), [ 'status' => 403 ] );
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

		$post_id = $request['post'];

		$user          = wp_get_current_user();
		$user_id_or_ip = null;

		if ( (int) $user->ID !== 0 ) {
			$user_id_or_ip = $user->ID;
		}


		$rate = AnyCommentRating::add_rating( $request['rating'], $post_id, $user_id_or_ip );

		if ( ! $rate ) {
			return new WP_Error( 'rest_rate_something_wrong', __( 'Sorry, something went wrong. Please try to rate again.', 'anycomment' ) );
		}

		$responseArray = [
			'value' => AnyCommentRating::get_average_by_post( $post_id ),
			'count' => AnyCommentRating::get_count_by_post( $post_id ),
		];

		$response = $this->prepare_item_for_response( $responseArray, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Prepares a single like output for response.
	 *
	 * @since 4.7.0
	 *
	 * @param array $rating_data List of uploaded file URLs.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $rating_data, $request ) {

		$data = $rating_data;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}
}