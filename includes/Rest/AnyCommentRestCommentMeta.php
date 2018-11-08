<?php

namespace AnyComment\Rest;


class AnyCommentRestCommentMeta extends \WP_REST_Meta_Fields {
	/**
	 * Retrieves the object type for comment meta.
	 *
	 * @since 4.7.0
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {
		return 'comment';
	}

	/**
	 * Retrieves the type for register_rest_field() in the context of comments.
	 *
	 * @since 4.7.0
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {
		return 'comment';
	}
}