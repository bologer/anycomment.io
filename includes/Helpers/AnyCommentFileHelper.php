<?php


namespace AnyComment\Helpers;

use InvalidArgumentException;

/**
 * AnyCommentFileHelper contains helpers for file management.
 *
 * @package AnyComment\Helpers
 */
class AnyCommentFileHelper {

	/**
	 * Delete given directory.
	 *
	 * @param string $dirPath
	 */
	public static function unlinkDirectory( $dirPath ) {
		if ( ! is_dir( $dirPath ) ) {
			throw new InvalidArgumentException( "$dirPath must be a directory" );
		}
		if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {
			$dirPath .= '/';
		}
		$files = glob( $dirPath . '*', GLOB_MARK );
		foreach ( $files as $file ) {
			if ( is_dir( $file ) ) {
				self::unlinkDirectory( $file );
			} else {
				unlink( $file );
			}
		}
		rmdir( $dirPath );
	}

}