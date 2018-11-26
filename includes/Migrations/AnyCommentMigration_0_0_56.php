<?php

namespace AnyComment\Migrations;

use WP_Error;

/**
 * Class AnyCommentMigration_0_0_56 is used to re upload big avatars and make them smaller in favor of speed.
 */
class AnyCommentMigration_0_0_56 extends AnyCommentMigration {
	public $table = 'uploaded_files';
	public $version = '0.0.56';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		return version_compare( get_option( 'anycomment_migration' ), $this->version, '>=' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		/**
		 * Create email queue table
		 */
		$sql = "SELECT * FROM $wpdb->usermeta WHERE `meta_key`='anycomment_social_avatar'";

		$results = $wpdb->get_results( $sql );

		if ( null === $results ) {
			return true;
		}

		if ( ! function_exists( 'wp_generate_password' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		foreach ( $results as $meta ) {
			if ( empty( $meta->meta_key ) ) {
				continue;
			}

			$path         = parse_url( $meta->meta_value, PHP_URL_PATH );
			$originalPath = str_replace( '//', '/', ABSPATH . $path );

			$imageEditor = wp_get_image_editor( $originalPath, [ 'mime_type' => 'image/jpeg' ] );

			if ( $imageEditor instanceof WP_Error ) {
				continue;
			}

			$sizes = $imageEditor->get_size();

			if ( $sizes['width'] > 60 || $sizes['height'] > 60 ) {
				$imageEditor->resize( 60, 60, true );

				$newName      = md5( serialize( $meta->meta_value ) );
				$croppedImage = $imageEditor->save( $newName, 'image/jpeg' );

				if ( $croppedImage instanceof WP_Error ) {
					continue;
				}

				$file = array(
					'name'     => $croppedImage['file'],
					'type'     => $croppedImage['mime-type'],
					'tmp_name' => $croppedImage['path'],
					'error'    => 0,
					'size'     => filesize( $croppedImage['path'] ),
				);

				$results = wp_handle_sideload( $file, [ 'test_form' => false, 'test_size' => true ] );

				if ( ! empty( $results['error'] ) ) {
					continue;
				}

				if ( update_user_meta( $meta->user_id, $meta->meta_key, $results['url'] ) ) {
					@unlink( $originalPath );
				}
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}
}

// eof;
