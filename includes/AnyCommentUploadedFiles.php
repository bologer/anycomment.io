<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentUploadedFiles helps to manage data in `anycomment_uploaded_files` table.
 *
 * @property int $ID
 * @property int $post_ID
 * @property int|null $user_ID
 * @property string|null $url
 * @property string|null $url_thumbnail Thumbnail of image. Used only for images.
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int $created_at
 *
 * @since 0.0.3
 */
class AnyCommentUploadedFiles {

	public $ID;
	public $post_ID;
	public $user_ID;
	public $url;
	public $url_thumbnail;
	public $ip;
	public $user_agent;
	public $created_at;

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function tableName() {
		return 'anycomment_uploaded_files';
	}

	/**
	 * Start query.
	 *
	 * @return wpdb
	 */
	public static function find() {
		global $wpdb;

		return $wpdb;
	}

	/**
	 * Find uploaded file by ID.
	 *
	 * @param int $id File ID to search for.
	 *
	 * @return null|$this NULL returned on failure and object on success.
	 */
	public static function findOne( $id ) {
		$tablaName     = static::tableName();
		$preparedQuery = static::find()->prepare( "SELECT * FROM $tablaName WHERE `id`=%d", [ $id ] );

		$res = static::find()->get_row( $preparedQuery );

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
	public static function isOverLimitByIp( $ip = null ) {
		global $wpdb;

		if ( $ip === null ) {
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		$seconds      = AnyCommentGenericSettings::getFileUploadLimit();
		$intervalTime = strtotime( "-{$seconds} seconds" );
		$limit        = AnyCommentGenericSettings::getFileLimit();

		$table_name = static::tableName();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `ip`=%s AND `created_at` >= %d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $ip, $intervalTime ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count >= $limit;
	}

	/**
	 * Delete single file or multiple at once.
	 *
	 * @param $data
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

		$table = static::tableName();
		$query = "DELETE FROM $table WHERE `id` IN ($ids)";

		$affected_rows = static::find()->query( $query );

		return $affected_rows >= 0;
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

		$tableName = static::tableName();

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
}