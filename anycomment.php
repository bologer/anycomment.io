<?php
/**
 * Plugin Name: AnyComment
 * Plugin URI: https://anycomment.io
 * Description: AnyComment is an advanced commenting system for WordPress.
 * Version: 0.0.72
 * Author: Bologer
 * Author URI: http://bologer.ru
 * Requires at least: 4.4
 * Requires PHP: 5.4
 * Tested up to: 5.0
 * Text Domain: anycomment
 * Domain Path: /languages
 *
 * @package AnyComment
 * @author bologer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

function AnyComment() {
	return \AnyComment\AnyCommentCore::instance();
}

AnyComment();
