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
	 * @param string $name Name of the template to get content of.
	 *
	 * @return mixed
	 */
	public static function render( $name ) {
		ob_start();
		include ANYCOMMENT_ABSPATH . "/templates/{$name}.php";
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}