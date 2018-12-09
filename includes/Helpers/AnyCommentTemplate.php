<?php

namespace AnyComment\Helpers;

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
	public static function render( $name ) {
		ob_start();

		if ( is_readable( $name ) ) {
			$path = $name;
		} else {
			$path = ANYCOMMENT_ABSPATH . "/templates/{$name}.php";
		}

		include $path;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}