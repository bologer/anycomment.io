<?php

// See https://github.com/bologer/WordPress-Hook-Parser
include __DIR__ . '/../../phpdoc-parser/vendor/autoload.php';
include __DIR__ . '/../../wordpress-hook-parser/vendor/autoload.php';

$hooksParser = new Bologer\HooksParser( [
	'scanDirectory'     => dirname( __FILE__ ) . '/../../anycomment',
	'ignoreDirectories' => [
		'vendor',
		'languages'
	]
] );

$parsedItems = $hooksParser->parse();

$hooksDocumentation = new Bologer\HookDocumentation( $parsedItems );
$hooksDocumentation->setSaveLocation( dirname( __FILE__ ) . '/../docs/hooks.md' );
$hooksDocumentation->write();



