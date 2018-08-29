<?php

class AnyCommentRestDocuments extends AnyCommentRestController {

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'anycomment/v1';
		$this->rest_base = 'documents';

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
			'args'   => [
				'post' => [
					'description' => __( "Post ID", 'anycomment' ),
					'type'        => 'int'
				],
				'name' => [
					'description' => __( 'File name', 'anycomment' ),
					'type'        => 'string'
				],
				'type' => [
					'description' => __( 'File type (e.g. image/jpeg)', 'anycomment' ),
					'type'        => 'string'
				],
				'size' => [
					'description' => __( 'File size in bytes', 'anycomment' ),
					'type'        => 'int'
				],
				'blob' => [
					'description' => __( 'Binary file data', 'anycomment' ),
					'type'        => 'string',
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_item_permissions_check( $request ) {

		$files = $request->get_file_params();

		if ( empty( $files ) ) {
			return new WP_Error( 'rest_missing_file_data', __( 'Missing file data', 'anycomment' ), [ 'status' => 403 ] );
		}

		if ( empty( $request['post'] ) ) {
			return new WP_Error( 'rest_comment_like_invalid_post_id', __( 'Sorry, post does not exist.', 'anycomment' ), array( 'status' => 403 ) );
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

		if ( AnyCommentUploadedFiles::isOverLimitByIp() ) {
			return new WP_Error( 'rest_comment_over_limit', __( 'Sorry, you have reached upload limit. Wait a little bit before uploading again.', 'anycomment' ), array( 'status' => 403 ) );
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

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$files         = $request->get_file_params();
		$uploaded_urls = [];

		foreach ( $files as $key => $file ) {
			$file_extension = strtolower( trim( end( explode( '.', $file['name'] ) ) ) );
			$file['name']   = sprintf( '%s.%s', md5( serialize( $file ) ), $file_extension );

			$moved_file = wp_handle_upload( $file, [ 'test_form' => false ] );

			if ( $moved_file && ! isset( $moved_file['error'] ) ) {
				$uploaded_urls[] = $moved_file['url'];
			}
		}

		if ( ! empty( $uploaded_urls ) ) {
			foreach ( $uploaded_urls as $key => $uploaded_url ) {
				$model          = new AnyCommentUploadedFiles();
				$model->post_ID = $request['post'];

				if ( ( $user = wp_get_current_user() ) !== null ) {
					$model->user_ID = $user->ID;
				}
				$model->url = $uploaded_url;

				if ( ! $model->save() ) {
					$path      = parse_url( $uploaded_url, PHP_URL_PASS );
					$full_path = get_home_path() . $path;
					wp_delete_file( $full_path );
				}
			}
		}

		$response = $this->prepare_item_for_response( $uploaded_urls, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Prepares a single like output for response.
	 *
	 * @since 4.7.0
	 *
	 * @param array $uploadedUrls List of uploaded file URLs.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $uploadedUrls, $request ) {

		$data = [];

		$data['urls'] = $uploadedUrls;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

}