<?php

namespace AnyComment\Rest;

/**
 * Class AnyCommentRestController serves as a wrapper for main
 * REST controller from WordPress.
 */
class AnyCommentRestController extends \WP_REST_Controller {
	/**
	 * @var string Current rest version.
	 */
	protected $version = 'v1';

	/**
	 * Get REST version.
	 *
	 * @return string
	 */
	protected function getNamespace() {
		return sprintf( 'anycomment/%s', $this->version );
	}
}