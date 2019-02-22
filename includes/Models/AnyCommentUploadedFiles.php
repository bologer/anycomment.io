<?php

namespace AnyComment\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\AnyCommentCommentMeta;
use AnyComment\Admin\AnyCommentGenericSettings;

/**
 * Class AnyCommentUploadedFiles helps to manage data in `anycomment_uploaded_files` table.
 *
 * @property int $ID
 * @property int $post_ID
 * @property int|null $user_ID
 * @property string $type MIME type.
 * @property string|null $url
 * @property string|null $url_thumbnail Thumbnail of image. Used only for images.
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int $created_at
 *
 * @since 0.0.3
 */
class AnyCommentUploadedFiles extends AnyCommentActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static $table_name = 'uploaded_files';

	const TYPE_AUDIO = 'audio';
	const TYPE_APPLICATION = 'document';
	const TYPE_IMAGE = 'image';

	public $ID;
	public $post_ID;
	public $user_ID;
	public $type;
	public $url;
	public $url_thumbnail;
	public $ip;
	public $user_agent;
	public $created_at;

	/**
	 * Find uploaded file by ID.
	 *
	 * @param int $id File ID to search for.
	 *
	 * @return null|AnyCommentUploadedFiles NULL returned on failure and object on success.
	 */
	public static function find_one( $id ) {
		global $wpdb;
		$tablaName     = static::get_table_name();
		$preparedQuery = $wpdb->prepare( "SELECT * FROM $tablaName WHERE ID = %d", [ $id ] );

		$res = $wpdb->get_row( $preparedQuery );

		if ( empty( $res ) ) {
			return null;
		}

		return $res;
	}

	/**
	 * Get likes count per comment.
	 *
	 * @param string $ip User IP address to check for.
	 *
	 * @return int
	 */
	public static function is_over_limit_by_ip( $ip = null ) {
		global $wpdb;

		if ( $ip === null ) {
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		$seconds      = AnyCommentGenericSettings::get_file_upload_limit();
		$intervalTime = strtotime( "-{$seconds} seconds" );
		$limit        = AnyCommentGenericSettings::get_file_limit();

		$table_name = static::get_table_name();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `ip`=%s AND `created_at` >= %d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $ip, $intervalTime ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count >= $limit;
	}

	/**
	 * Delete single file or multiple files at once.
	 *
	 * @param array|string $data Single or list of IDs to delete.
	 *
	 * @return bool
	 */
	public static function delete( $data ) {

		if ( empty( $data ) ) {
			return false;
		}

		if ( is_array( $data ) ) {
			$ids = implode( ',', $data );
		} else {
			$ids = $data;
		}

		global $wpdb;

		$table = static::get_table_name();
		$sql   = "SELECT * FROM `$table` WHERE `id` IN ($ids)";

		$files = $wpdb->get_results( $sql );

		if ( empty( $files ) ) {
			return true;
		}

		$deletedCount = 0;

		/**
		 * @var AnyCommentUploadedFiles $file
		 */
		foreach ( $files as $file ) {
			// Delete original file
			$path = static::get_path_from_url( $file->url );
			@unlink( $path );

			// Delete file thumbnail, if exists
			if ( ! empty( $file->url_thumbnail ) ) {
				$thumbnail_path = static::get_path_from_url( $file->url_thumbnail );
				@unlink( $thumbnail_path );
			}

			// Delete file attachment from comment meta (if there are such)
			AnyCommentCommentMeta::delete_attachment_by_file_id( $file->ID );

			// Delete files from table
			$affected_rows = $wpdb->delete( $table, [ 'id' => $file->ID ] );

			if ( $affected_rows > 0 ) {
				$deletedCount ++;
			}
		}

		return $deletedCount === count( $files );
	}


	/**
	 * Inserts uploaded file to track limits, etc.
	 *
	 * @since 0.0.52
	 *
	 * @return false|AnyCommentLikes|object
	 */
	public function save() {
		if ( ! isset( $this->ip ) ) {
			$this->ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
		}

		if ( ! isset( $this->user_agent ) ) {
			$this->user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
		}

		if ( ! isset( $this->created_at ) ) {
			$this->created_at = time();
		}

		global $wpdb;

		$tableName = static::get_table_name();

		unset( $this->ID );

		$count = $wpdb->insert( $tableName, (array) $this );


		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$this->ID = $lastId;

			return $this;
		}

		return false;
	}

	/**
	 * Check whether specified MIME type can be cropped.
	 *
	 * @param string $mime_type MIME type to check.
	 *
	 * @return bool
	 */
	public static function can_crop( $mime_type ) {
		$mime_type = trim( $mime_type );

		if ( empty( $mime_type ) ) {
			return false;
		}

		$cropSupport = [
			'image/jpeg' => 'image/jpeg',
			'image/png'  => 'image/png',
			'image/gif'  => 'image/gif',
		];

		return isset( $cropSupport[ $mime_type ] );
	}

	/**
	 * Get image type.
	 *
	 * @param string $mime_type File MIME type to get the type.
	 *
	 * @return string
	 */
	public static function get_image_type( $mime_type ) {
		if ( static::is_image_by( $mime_type ) ) {
			return self::TYPE_IMAGE;
		} elseif ( static::is_audio_by( $mime_type ) ) {
			return self::TYPE_AUDIO;
		}

		return self::TYPE_APPLICATION;
	}

	/**
	 * Check whether MIME type is related to image.
	 *
	 * @param string $mime_type MIME type to check for.
	 *
	 * @return bool
	 */
	public static function is_image_by( $mime_type ) {
		return strpos( $mime_type, 'image/' ) !== false;
	}

	/**
	 * Check whether mime type is audio.
	 *
	 * @param string $mime_type MIME type to check for.
	 *
	 * @return bool
	 */
	public static function is_audio_by( $mime_type ) {
		return strpos( $mime_type, 'audio/' ) !== false;
	}


	/**
	 * Get path from file URL.
	 *
	 * @param string $url URL where to retrieve the path.
	 *
	 * @return string
	 */
	public static function get_path_from_url( $url ) {
		$path = parse_url( $url );

		return ABSPATH . $path['path'];
	}
}
