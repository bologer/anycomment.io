<?php

namespace AnyComment;

class AnyCommentMutex {

	private static $openedHandlers = [];

	/**
	 * Create lock with name.
	 *
	 * @param string $lockName
	 *
	 * @return bool
	 * @throws \Exception on failure to acquire lock.
	 */
	public static function acquire( $lockName ) {
		$upload_dir                        = wp_upload_dir();
		$lockPath                          = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $lockName . '.lock';
		$fp                                = fopen( $lockPath, 'w+' );
		self::$openedHandlers[ $lockName ] = [
			'resource'     => $fp,
			'fileLocation' => $lockPath
		];
		if ( ! flock( $fp, LOCK_EX | LOCK_NB ) ) {
			return false;
		}
		ftruncate( $fp, 0 );
		fwrite( $fp, microtime( true ) );

		return true;
	}

	/**
	 * Release lock.
	 *
	 * @param string $lockName
	 *
	 * @return bool
	 * @throws \Exception when lock does not exist.
	 */
	public static function release( $lockName ) {
		if ( array_key_exists( $lockName, self::$openedHandlers ) ) {
			$resource = self::$openedHandlers[ $lockName ]['resource'];
			if ( is_resource( $resource ) ) {
				fflush( $resource );
				flock( $resource, LOCK_UN );
				fclose( $resource );
				unlink( self::$openedHandlers[ $lockName ]['fileLocation'] );

				return true;
			}
		}

		return false;
	}
}
