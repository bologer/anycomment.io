<?php

namespace AnyComment\Base;

/**
 * Class Request is a singletone which helps to work with request.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Base
 */
class Request extends BaseObject {
	/**
	 * Get specific get param value or the whole array.
	 *
	 * @param null $name Get parameter name.
	 *
	 * @return array|string|null Array GET param is in array format or $name is null. String when GET parameter
	 * is not empty.
	 */
	public function get( $name = null ) {
		if ( $name === null ) {
			return $_GET;
		}

		if ( ! array_key_exists( $name, $_GET ) ) {
			return null;
		}

		return sanitize_text_field( $_GET[ $name ] );
	}

	/**
	 * Get specific get param value or the whole array.
	 *
	 * @param null $name POST parameter name.
	 *
	 * @return array|string|null Array POST param is in array format or $name is null. String when POST parameter
	 * is not empty.
	 */
	public function post( $name = null ) {
		if ( $name === null ) {
			return $_POST;
		}

		if ( ! array_key_exists( $name, $_POST ) ) {
			return null;
		}

		return sanitize_text_field( $_POST[ $name ] );
	}
}
