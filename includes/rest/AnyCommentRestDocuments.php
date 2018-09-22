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
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/delete', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			],
			'args'   => [
				'id' => [
					'description' => __( 'File ID', 'anycomment' ),
					'type'        => 'int'
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete_item( $request ) {
		$rowsAffected = AnyCommentUploadedFiles::delete( $request['id'] );

		if ( $rowsAffected <= 0 ) {
			return new WP_Error( 'rest_failed_to_delete', __( 'Error, unable to delete. Please try again later.', 'anycomment' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete_item_permissions_check( $request ) {

		$user = wp_get_current_user();

		// Make sure action is NOT done by guest
		if ( ! isset( $user->ID ) || isset( $user->ID ) && (int) $user->ID === 0 ) {
			return new WP_Error( 'rest_no_guests_allowed', __( 'Sorry, guests unable to delete files.', 'anycomment' ), [ 'status' => 403 ] );
		}

		$uploaded_file = AnyCommentUploadedFiles::findOne( $request['id'] );

		// Make sure file can be found
		if ( $uploaded_file === null ) {
			return new WP_Error( 'rest_file_not_found', __( 'Files does not exist.', 'anycomment' ), [ 'status' => 403 ] );
		}

		// If file has user ID, check to make sure it is the same user trying to delete the file
		if ( $uploaded_file->user_ID !== null && (int) $uploaded_file->user_ID !== (int) $user->ID ) {
			return new WP_Error( 'rest_wrong_user', __( 'Sorry, such file is unavailable', 'anycomment' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! is_user_logged_in() && ! AnyCommentGenericSettings::isGuestCanUpload() ) {
			return new WP_Error( 'rest_guest_unable_to_upload', __( 'Sorry, guest users cannot upload files', 'anycomment' ), [ 'status' => 403 ] );
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

		$files = $request->get_file_params();

		if ( empty( $files ) ) {
			return new WP_Error( 'rest_missing_file_data', __( 'Missing file data', 'anycomment' ), [ 'status' => 403 ] );
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}


		$max_size_allowed = AnyCommentGenericSettings::getFileMaxSize() * 1000000;
		$uploaded_files   = [];

		foreach ( $files as $key => $file ) {
			// When size is bigger then allowed, skip it
			if ( $file['size'] > $max_size_allowed ) {
				continue;
			}

			if ( ! AnyCommentGenericSettings::isAllowedMimeType( $file ) ) {
				continue;
			}

			$original_file = AnyCommentUploadHandler::save( $file );

			if ( is_wp_error( $original_file ) ) {
				continue;
			}

			$file_mime_type = $file['type'];
			$cropSupport    = [
				'image/jpeg' => 'image/jpeg',
				'image/png'  => 'image/png',
				'image/gif'  => 'image/gif',
			];

			$is_image = $this->is_image_by( $file_mime_type );
			$is_audio = $this->is_audio_by( $file_mime_type );

			$uploaded_files[ $key ] = [
				'isImage'    => $is_image,
				'isAudio'    => $is_audio,
				'isDocument' => ! $is_image && ! $is_audio,
				'src'        => $original_file['url']
			];

			$should_create_thumbnail = isset( $cropSupport[ $file_mime_type ] );

			if ( $should_create_thumbnail ) {
				// Get smaller version of file
				$imageEditor = wp_get_image_editor( $original_file['file'], [ 'mime_type' => $file_mime_type ] );

				if ( is_wp_error( $imageEditor ) ) {
					continue;
				}

				$imageEditor->resize( 60, 60, true );

				$thumbnail_name = AnyCommentUploadHandler::get_file_name( 'thumbnail_' . $file['name'] );

				$upload_dir = wp_get_upload_dir();

				// When unable to get upload dir, should delete original file as well
				if ( $upload_dir['error'] !== false ) {
					wp_delete_file( $original_file['file'] );
					continue;
				}

				$savePath = $upload_dir['path'] . DIRECTORY_SEPARATOR . $thumbnail_name;

				$croppedImage = $imageEditor->save( $savePath, $file_mime_type );

				if ( ! is_wp_error( $croppedImage ) ) {
					$uploaded_files[ $key ]['thumbnail'] = $upload_dir['url'] . DIRECTORY_SEPARATOR . $croppedImage['file'];
				}
			}
		}

		// Process saving uploaded files into the database
		if ( ! empty( $uploaded_files ) ) {
			foreach ( $uploaded_files as $key => $upload ) {
				$model          = new AnyCommentUploadedFiles();
				$model->post_ID = $request['post'];

				$user = wp_get_current_user();

				if ( isset( $user->ID ) && (int) $user->ID !== 0 ) {
					$model->user_ID = $user->ID;
				}
				$model->url = $upload['src'];

				if ( isset( $upload['thumbnail'] ) ) {
					$model->url_thumbnail = $upload['thumbnail'];
				}

				if ( ! $model->save() ) {
					wp_delete_file( $this->get_path_from_url( $model->url ) );

					if ( isset( $upload['thumbnail'] ) ) {
						wp_delete_file( $this->get_path_from_url( $model->url_thumbnail ) );
					}
				} else {
					$uploaded_files[ $key ]['id'] = $model->ID;
				}
			}
		}

		$response = $this->prepare_item_for_response( $uploaded_files, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Check whether MIME type is related to image.
	 *
	 * @param string $mime_type MIME type to check for.
	 *
	 * @return bool
	 */
	public function is_image_by( $mime_type ) {
		return strpos( $mime_type, 'image/' ) !== false;
	}

	/**
	 * Check whether mime type is audio.
	 *
	 * @param string $mime_type MIME type to check for.
	 *
	 * @return bool
	 */
	public function is_audio_by( $mime_type ) {
		return strpos( $mime_type, 'audio/' ) !== false;
	}


	/**
	 * Get path from file URL.
	 *
	 * @param string $url URL where to retrieve the path.
	 *
	 * @return string
	 */
	public function get_path_from_url( $url ) {
		$path = parse_url( $url, PHP_URL_PASS );

		return get_home_path() . $path;
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

		$data['files'] = $uploadedUrls;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}


}