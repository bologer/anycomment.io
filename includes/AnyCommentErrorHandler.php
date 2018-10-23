<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnyCommentErrorHandler' ) ):
	/**
	 * AnyCommentErrorHandler is processing errors.
	 */
	class AnyCommentErrorHandler {

		/**
		 * @var string Cookie name.
		 */
		private $_cookie_name = 'anycomment-errors';

		/**
		 * Check whether there are some errors or not.
		 *
		 * @return bool
		 */
		public function has_errors() {
			return $this->getCookie() !== null;
		}

		/**
		 * Get list of errors as string or array.
		 *
		 * @package bool $asArray Set true when required to get list of errors in form of array.
		 * @return null|string
		 */
		public function get_errors( $asArray = false ) {
			$value = $this->getCookie();

			if ( $asArray ) {
				return json_encode( $value );
			}

			return $value;
		}

		/**
		 * Add new error to the list.
		 *
		 * @param string $message Message to be added to the list.
		 *
		 * @return bool
		 */
		public function add_error( $message ) {
			return $this->set_error( $message );
		}

		/**
		 * Get current cookie value.
		 * @return null|string NULL when it is not set.
		 */
		private function getCookie() {
			return isset( $_COOKIE[ $this->get_cookie_name() ] ) ? $_COOKIE[ $this->get_cookie_name() ] : null;
		}

		/**
		 * Get cookie name.
		 *
		 * @return string
		 */
		public function get_cookie_name() {
			return $this->_cookie_name;
		}

		/**
		 * Clean error messages.
		 *
		 * @return bool
		 */
		public function clean_errors() {
			setcookie( $this->get_cookie_name(), null, time() - 3600, '/' );

			return ! isset( $_COOKIE[ $this->get_cookie_name() ] );
		}

		/**
		 * Set error message.
		 *
		 * @param string $message Error message to be added.
		 *
		 * @return bool False will be returned when message is empty.
		 */
		private function set_error( $message ) {
			if ( ( $value = $this->process_errors( $message ) ) === null ) {
				return false;
			}

			setcookie( $this->get_cookie_name(), $value, 0, '/' );

			return true;
		}

		/**
		 * Process errors.
		 *
		 * @param string $message Error message.
		 *
		 * @return string Prepared list of errors in JSON format.
		 */
		private function process_errors( $message ) {
			$errors  = $this->getCookie();
			$message = trim( $message );

			if ( empty( $message ) ) {
				return null;
			}

			if ( empty( $errors ) ) {
				return json_encode( [ $message ] );
			}

			$errorList = json_decode( $errors, true );

			if ( empty( $errorList ) ) {
				return json_encode( [ $message ] );
			}

			$errorList[] = $message;

			return json_encode( $errorList );
		}
	}

endif;