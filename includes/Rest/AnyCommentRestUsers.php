<?php

namespace AnyComment\Rest;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_User;

class AnyCommentRestUsers extends AnyCommentRestController {
	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = $this->getNamespace();
		$this->rest_base = 'users';

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/me', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_current_item' ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * Retrieves the current user.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_current_item( $request ) {
		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return new WP_Error( 'rest_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
		}

		$user     = wp_get_current_user();
		$response = $this->prepare_item_for_response( $user, $request );
		$response = rest_ensure_response( $response );


		return $response;
	}

	/**
	 * Prepares a single user output for response.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_User $user User object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $user, $request ) {

		$data = [
			'id'         => $user->ID,
			'username'   => $user->user_login,
			'name'       => $user->display_name,
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
			'avatar_url' => AnyCommentSocialAuth::get_user_avatar_url( $user->ID )
		];

		$context = ! empty( $request['context'] ) ? $request['context'] : 'embed';

		$data = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}
}