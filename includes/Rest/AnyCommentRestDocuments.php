<?php

namespace AnyComment\Rest;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\AnyCommentUploadHandler;
use AnyComment\Models\AnyCommentUploadedFiles;

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
		AnyCommentUploadedFiles::delete( $request['id'] );

		$data = [
			'success' => true
		];

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
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

		$file = AnyCommentUploadedFiles::find_one( $request['id'] );

		// Make sure file can be found
		if ( $file === null ) {
			return new WP_Error( 'rest_file_not_found', __( 'File does not exist.', 'anycomment' ), [ 'status' => 403 ] );
		}

		// If file has user ID, check to make sure it is the same user trying to delete the file
		if ( $file->user_ID !== null && (int) $file->user_ID !== (int) $user->ID ) {
			return new WP_Error( 'rest_wrong_user', __( 'Sorry, such file is unavailable', 'anycomment' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! $this->can_upload() ) {
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

		if ( AnyCommentUploadedFiles::is_over_limit_by_ip() ) {
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


		$max_size_allowed = AnyCommentGenericSettings::get_file_max_size() * 1000000;
		$uploaded_files   = [];

		foreach ( $files as $key => $file ) {
			// When size is bigger then allowed, skip it
			if ( $file['size'] > $max_size_allowed ) {
				continue;
			}

			if ( ! AnyCommentGenericSettings::is_allowed_mime_type( $file ) ) {
				continue;
			}

			$original_file = AnyCommentUploadHandler::save( $file );

			if ( is_wp_error( $original_file ) ) {
				continue;
			}

			$file_mime_type = $file['type'];

			$uploaded_files[ $key ] = [
				'file_type' => AnyCommentUploadedFiles::get_image_type( $file_mime_type ),
				'file_mime' => $file_mime_type,
				'file_url'  => $original_file['url']
			];

			if ( AnyCommentUploadedFiles::can_crop( $file_mime_type ) ) {
				// Get smaller version of file
				$imageEditor = wp_get_image_editor( $original_file['file'], [ 'mime_type' => $file_mime_type ] );

				if ( !is_wp_error( $imageEditor ) ) {

                    $imageEditor->resize( 60, 60, true );

                    $savePath = AnyCommentUploadHandler::get_save_dir();

                    if($savePath === null) {
                        wp_delete_file($original_file['file']);
                        continue;
                    }

                    $thumbnail_name = AnyCommentUploadHandler::get_file_name( 'thumbnail_' . $file['name'] );

                    $savePath .= DIRECTORY_SEPARATOR . $thumbnail_name;

                    $croppedImage = $imageEditor->save( $savePath, $file_mime_type );

                    if ( ! is_wp_error( $croppedImage ) ) {
                        $serve_url = AnyCommentUploadHandler::get_serve_url();

                        $uploaded_files[ $key ]['file_thumbnail'] = $serve_url . '/' . $thumbnail_name;
                    }
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
				$model->type = $upload['file_mime'];
				$model->url  = $upload['file_url'];

				if ( isset( $upload['file_thumbnail'] ) ) {
					$model->url_thumbnail = $upload['file_thumbnail'];
				}

				if ( ! $model->save() ) {
					wp_delete_file( AnyCommentUploadedFiles::get_path_from_url( $model->url ) );

					if ( isset( $upload['file_thumbnail'] ) ) {
						wp_delete_file( AnyCommentUploadedFiles::get_path_from_url( $model->url_thumbnail ) );
					}
				} else {
					$uploaded_files[ $key ]['file_id'] = $model->ID;
				}
			}
		}

		$response = $this->prepare_item_for_response( $uploaded_files, $request );
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

		$data['files'] = $uploadedUrls;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Check whether current user can upload files or not.
	 *
	 * @return bool
	 */
	public function can_upload() {
		$is_file_upload_allowed = AnyCommentGenericSettings::is_file_upload_allowed();
		$is_guest               = ! is_user_logged_in();

		if ( $is_file_upload_allowed && ! $is_guest ) {
			return true;
		} else if ( $is_file_upload_allowed && $is_guest && AnyCommentGenericSettings::is_guest_can_upload() ) {
			return true;
		}

		return false;
	}
}