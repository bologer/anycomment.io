<?php

namespace AnyComment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_Error;
use AnyComment\Admin\AnyCommentGenericSettings;

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
	 * @return WP_Error|string|array WP_Error in case of error. String is
	 */
	public static function save( $file ) {

		if ( ! isset( $file['error'] ) ) {
			$file['error'] = 0;
		}

		// When no size defined
		if ( ! isset( $file['size'] ) && isset( $file['tmp_name'] ) ) {
			$file['size'] = @filesize( $file['path'] );
		}

		$file_name = static::get_file_name( $file['name'] );

		if ( is_wp_error( $file_name ) ) {
			return $file_name;
		}

		$file['name'] = $file_name;

        $save_path = static::get_save_dir();

        $error = new WP_Error('error_uploading', 'Unable to upload file');

        if($save_path === null) {
            return $error;
        }

        $absolute_save_path = $save_path . DIRECTORY_SEPARATOR . $file_name;

        if(!@copy($file['tmp_name'], $absolute_save_path)) {
            return $error;
        }

        $serve_url = static::get_serve_url();

        $serve_url .= '/' . $file_name;


        return [
            'url' => $serve_url,
            'file' => $absolute_save_path,
            'type' => $file['type']
        ];
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

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		// Download file to temp dir
		$temp_file = download_url( $profileUrl, 5 );

		if ( is_wp_error( $temp_file ) ) {
			return false;
		}

		$instance = wp_get_image_editor( $temp_file, [ 'mime_type' => 'image/jpeg' ] );

		if ( $instance instanceof WP_Error ) {
			return false;
		}

		$savePath = static::get_save_dir();

		if($savePath === null) {
            wp_delete_file( $temp_file['file'] );
            return false;
        }

        $fileName = static::getFileName( $metaIdentifier );

		$savePath = $savePath . DIRECTORY_SEPARATOR . $fileName;

		$instance->resize( AnyCommentAvatars::DEFAULT_AVATAR_WIDTH, AnyCommentAvatars::DEFAULT_AVATAR_HEIGHT, true );

		$croppedImage = $instance->save( $savePath, 'image/jpeg' );

		if ( $croppedImage instanceof WP_Error ) {
			return false;
		}

		return static::get_serve_url() . '/' . $fileName;
	}

    /**
     * Get save directory.
     *
     * When options save directory specified - it would be used, otherwise default save path would be used.
     *
     * @return string|null
     */
	public static function get_save_dir() {
	    $options_save_path = AnyCommentGenericSettings::get_file_save_path();

	    if(!empty($options_save_path)) {
	        return $options_save_path;
        }

	    return static::get_default_save_dir();
    }

    /**
     * Returns URL where file can be served.
     *
     * When URL in options is not specified, it uses default folder.
     *
     * @return string|null
     */
    public static function get_serve_url() {
        $options_serve_url = AnyCommentGenericSettings::get_file_save_serve_url();

        if(!empty($options_serve_url)) {
            return $options_serve_url;
        }

        return static::get_default_server_dir();
    }

    /**
     * Returns absolute default save directory path.
     *
     * @return string|null
     */
	public static function get_default_save_dir() {
        $upload_dir = wp_get_upload_dir();

        if(isset($upload_dir['error']) && !empty($upload_dir['error'])) {
            return null;
        }

        $ds = DIRECTORY_SEPARATOR;

        $path = $upload_dir['basedir'] . $ds . 'anycomment' . $ds . date('Y') . $ds . date('m');

        // When path does not exist, we should create it
        if(!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        return $path;
    }

    /**
     * Returns default URL where file can be served.
     *
     * @return string|null
     */
    public static function get_default_server_dir() {
        $upload_dir = wp_get_upload_dir();

        if(isset($upload_dir['error']) && !empty($upload_dir['error'])) {
            return null;
        }

        return $path = $upload_dir['baseurl'] . '/anycomment/' . date('Y') . '/' . date('m');
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
