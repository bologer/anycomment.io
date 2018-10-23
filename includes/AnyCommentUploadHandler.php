<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentUploadHandler is used to handle avatar upload for social medias
 * as some of the give access to the avatar for the period of access token.
 *
 * @since 0.0.3
 */
class AnyCommentUploadHandler {

	/**
	 * Get file name.
	 *
	 * @param string $name
	 *
	 * @return WP_Error|string
	 */
	public static function get_file_name( $name ) {
		$check_file_type = wp_check_filetype( $name );

		$file_extension = $check_file_type['ext'];

		// When unable to get extension, should skip
		if ( ! $file_extension ) {
			return new WP_Error( 'no_file_extension_found', 'Unable to find file extension' );
		}

		return sprintf( '%s.%s', md5( serialize( $name ) . time() ), $file_extension );
	}

	/**
	 * Generic method to handle file upload.
	 *
	 * @param array $file $_FILE array or can be passed manually. See array format in the note below:
	 * [
	 *  - name
	 *  - type
	 *  - tmp_name
	 *  - error
	 *  - size
	 * ]
	 *
	 * @param string $return Can be all, to return array,
	 * - `all` will return all of the data below
	 * - `url` URL to the file
	 * - `file` absolute path to the file
	 * - `type` file's MIME type
	 *
	 * @return WP_Error|string|array WP_Error in case of error. String is
	 */
	public static function save( $file, $return = 'all' ) {

		if ( ! isset( $file['error'] ) ) {
			$file['error'] = 0;
		}

		// When no size defined
		if ( ! isset( $file['size'] ) && isset( $file['tmp_name'] ) ) {
			$file['size'] = filesize( $file['path'] );
		}

		$file_name = static::get_file_name( $file['name'] );

		if ( is_wp_error( $file_name ) ) {
			return $file_name;
		}

		$file['name'] = $file_name;

		$moved_file = wp_handle_sideload( $file, [ 'test_form' => false, 'test_size' => true ] );

		if ( $moved_file && ! isset( $moved_file['error'] ) ) {
			if ( $return === 'all' ) {
				return $moved_file;
			}

			return $moved_file[ $return ];
		}

		$error = isset( $moved_file['error'] ) ? $moved_file['error'] : 'Unable to upload file';

		return new WP_Error( 'error_uploading', $error );
	}

	/**
	 * Upload specified image.
	 *
	 * @param string $profileUrl Profile URL used to upload image and return local URL to it.
	 * @param array $metaIdentifier Some list of params to be used as unique identifier of the avatar.
	 *
	 * @link https://wordpress.stackexchange.com/a/251512
	 * @return bool
	 */
	public static function upload_avatar( $profileUrl, $metaIdentifier ) {
		$profileUrl = trim( $profileUrl );

		if ( empty( $profileUrl ) ) {
			return false;
		}

		$timeout_seconds = 5;

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		// Download file to temp dir
		$temp_file = download_url( $profileUrl, $timeout_seconds );

		if ( is_wp_error( $temp_file ) ) {
			return false;
		}

		$instance = wp_get_image_editor( $temp_file, [ 'mime_type' => 'image/jpeg' ] );

		if ( $instance instanceof WP_Error ) {
			return false;
		}


		$upload_dir = wp_get_upload_dir();

		// When unable to get upload dir, should delete original file as well
		if ( $upload_dir['error'] !== false ) {
			wp_delete_file( $temp_file['file'] );

			return false;
		}

		$fileName = static::getFileName( $metaIdentifier );
		$savePath = $upload_dir['path'] . DIRECTORY_SEPARATOR . $fileName;

		$instance->resize( AnyCommentAvatars::DEFAULT_AVATAR_WIDTH, AnyCommentAvatars::DEFAULT_AVATAR_HEIGHT, true );

		$croppedImage = $instance->save( $savePath, 'image/jpeg' );

		if ( $croppedImage instanceof WP_Error ) {
			return false;
		}

		// Array based on $_FILE as seen in PHP file uploads
		$file = array(
			'name'     => $croppedImage['file'],
			'type'     => $croppedImage['mime-type'],
			'tmp_name' => $croppedImage['path'],
			'error'    => 0,
			'size'     => filesize( $croppedImage['path'] ),
		);

		$overrides = [
			// Tells WordPress to not look for the POST form
			// fields that would normally be present as
			// we downloaded the file from a remote server, so there
			// will be no form fields
			// Default is true
			'test_form' => false,

			// Setting this to false lets WordPress allow empty files, not recommended
			// Default is true
			'test_size' => true,
		];

		// Move the temporary file into the uploads directory
		$results = wp_handle_sideload( $file, $overrides );

		if ( ! empty( $results['error'] ) ) {
			return false;
		}

		unlink( $temp_file );

		$localUrl = $results['url'];  // URL to the file in the uploads dir

		// Perform any actions here based in the above results
		return $localUrl;
	}


	/**
	 * Generate file name based on passed meta data.
	 * File name could not be 100% in this method.
	 * This method is considered to be used together with wp_handle_sideload(),
	 * which is responsible for uniquness and postfix file when non-unique.
	 *
	 * @param array $meta Some meta data to be used to generate the hash.
	 *
	 * @return false|string False on failure.
	 */
	public static function getFileName( $meta ) {
		if ( empty( $meta ) ) {
			return false;
		}

		$meta[]   = uniqid( time() );
		$fileName = md5( serialize( $meta ) );

		return sprintf( '%s.jpg', $fileName );
	}
}