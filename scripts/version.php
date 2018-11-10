<?php
/**
 * Script is used to replace versions in files to prepare for release.
 */

$script = $argv[0];

if ( ! isset( $argv[1] ) ) {
	o( "No version defined" );
	exit( 1 );
}

if ( $argc < 2 ) {
	o( sprintf( "Usage: %s <version> (e.g. 1.1.1)", $script ) );
	exit( 1 );
}

$version = $argv[1];

/**
 * Map of files to be replaced.
 */
$date  = date( 'd.m.Y' );
$paths = [
	__DIR__ . '/../anycomment.php'              => [
		[ 'regex' => '/Version:\s[0-9.]+/m', 'replacement' => 'Version: %s' ],
	],
	__DIR__ . '/../includes/AnyCommentCore.php' => [
		[ 'regex' => '/\$version\s=\s\'([0-9.]+)\'/m', 'replacement' => '$version = \'%s\'' ],
	],
	__DIR__ . '/../readme.txt'                  => [
		[ 'regex' => '/Stable\stag:\s[0-9.]+/m', 'replacement' => 'Stable tag: %s' ],
		[
			'regex'       => '/==\sChangelog\s==/',
			'replacement' => "== Changelog ==\n\n= $version – $date =\n\n**Enhancements:**\n\n\n\n**Fixes:**\n\n"
		]
	],
	__DIR__ . '/../CHANGELOG.md'                => [
		[
			'regex'       => '/\#\sChangelog/',
			'replacement' => "# Changelog\n\n## $version – $date\n\n**Enhancements:**\n\n\n\n**Fixes:**\n\n"
		]
	]
];

if ( changeVersion( $paths, $version ) ) {
	exit( 0 );
}

exit( 1 );


/**
 * Change version.
 *
 * @param array $paths Paths to be searched and replaced with new version.
 * @param string $version Version to be changed to.
 *
 * @return bool
 */
function changeVersion( $paths, $version ) {
	if ( empty( $paths ) ) {
		o( sprintf( "No paths defined in %s", __FUNCTION__ ) );

		return false;
	}

	if ( empty( $version ) ) {
		o( sprintf( "No version to change to defined in %s", __FUNCTION__ ) );
	}

	foreach ( $paths as $path => $arr ) {
		if ( ! is_readable( $path ) ) {
			o( sprintf( "File %s is not readable", $path ) );

			return false;
		}

		if ( ! is_writable( $path ) ) {
			o( sprintf( "File %s is not writable", $path ) );

			return false;
		}

		if ( empty( $arr ) ) {
			o( sprintf( "Skipping %s path as it does not contain any regex replacements", $path ) );
			continue;
		}

		foreach ( $arr as $regParams ) {
			$regex       = $regParams['regex'];
			$replacement = sprintf( $regParams['replacement'], $version );

			$content = file_get_contents( $path );
			$content = preg_replace( $regParams['regex'], $replacement, $content );

			o( sprintf( "Replaced %s with %s in file %s now putting content back", $regex, $replacement, $path ) );

			file_put_contents( $path, $content );

			o( sprintf( "File %s should be replaced now", $path ) );
		}
	}

	return true;
}

/**
 * Output message to console.
 *
 * @param string $message Message.
 */
function o( $message ) {
	echo sprintf( '[LOG]: %s', $message . PHP_EOL );
}


