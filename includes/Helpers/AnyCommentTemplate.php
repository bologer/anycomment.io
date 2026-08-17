<?php

namespace AnyComment\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentTemplate is a core class for templating in AnyComment.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Helpers
 */
class AnyCommentTemplate {

	/**
	 * Render and return content of the template.
	 *
	 * @param string $name Name of the template to get content of. Could be absolute or relative path. When relative
	 * path used, it will be based on plugins /templates/ dir, absolute will be used as it is.
	 *
	 * @return mixed
	 */
	public static function render ( $name ) {
		ob_start();

		// Reject directory traversal and null bytes before $name is used to build
		// an include path below. Callers may pass request data: for example
		// templates/admin/tabs.php renders 'admin/tab-' . $_GET['tab'], and
		// sanitize_text_field() does not strip '../'.
		if ( ! is_callable( $name ) && ( ! is_string( $name ) || strpos( $name, "\0" ) !== false || strpos( $name, '..' ) !== false ) ) {
			ob_end_clean();

			return '';
		}

		if ( is_callable( $name ) ) {
			echo call_user_func( $name );
		} elseif ( is_file( $name ) || is_file( $name . '.php' ) ) {
			if ( is_file( $name ) ) {
				$path = $name;
			} else {
				$path = $name . '.php';
			}
		} else {
			$path = ANYCOMMENT_ABSPATH . "/templates/{$name}.php";
		}

		if ( ! is_file( $path ) ) {
			return '';
		}

		// Make sure the resolved template really lives inside the plugin
		// directory. This also covers the branch above, which accepts an
		// absolute path as-is.
		$real_path = realpath( $path );
		$base_path = realpath( ANYCOMMENT_ABSPATH );

		if ( $real_path === false || $base_path === false || strpos( $real_path, $base_path ) !== 0 ) {
			ob_end_clean();

			return '';
		}

		include $path;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
