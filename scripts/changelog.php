<?php
/**
 * Script is used to replace versions in files to prepare for release.
 */

$script = $argv[0];

if ( ! isset( $argv[1] ) ) {
	o( "No type defined (-e - for enhancement or -f - for fix)" );
	exit( 1 );
}

if ( ! isset( $argv[2] ) ) {
	o( "No message defined" );
	exit( 1 );
}

if ( $argc < 3 ) {
	o( sprintf( "Usage: %s <type> <message>", $script ) );
	exit( 1 );
}

$type    = $argv[1];
$message = $argv[2];




//preg_match('/\#\#\s[0-9.]\s–\s\d{2}\.\d{2}\.\d{4}/', $)


/**
 * Output message to console.
 *
 * @param string $message Message.
 */
function o( $message ) {
	echo sprintf( '[LOG]: %s', $message . PHP_EOL );
}