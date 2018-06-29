<?php


if ( ! class_exists( 'AnyCommentErrorHandler' ) ):
	/**
	 * AnyCommentErrorHandler is processing errors.
	 */
	class AnyCommentErrorHandler {
		private $_cookieName = 'anycomment-errors';

		/**
		 * Check whether there are some errors or not.
		 *
		 * @return bool
		 */
		public function hasErrors() {
			return $this->getCookie() !== null;
		}

		/**
		 * Get list of errors as string or array.
		 *
		 * @package bool $asArray Set true when required to get list of errors in form of array.
		 * @return null|string
		 */
		public function getErrors( $asArray = false ) {
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
		public function addError( $message ) {
			return $this->setError( $message );
		}

		/**
		 * Get current cookie value.
		 * @return null|string NULL when it is not set.
		 */
		private function getCookie() {
			return isset( $_COOKIE[ $this->getCookieName() ] ) ? $_COOKIE[ $this->getCookieName() ] : null;
		}

		/**
		 * Get cookie name.
		 *
		 * @return string
		 */
		public function getCookieName() {
			return $this->_cookieName;
		}

		/**
		 * Clean error messages.
		 *
		 * @return bool
		 */
		public function cleanErrors() {
			setcookie( $this->getCookieName(), null, time() - 3600, '/' );

			return ! isset( $_COOKIE[ $this->getCookieName() ] );
		}

		/**
		 * Set error message.
		 *
		 * @param string $message Error message to be added.
		 *
		 * @return bool False will be returned when message is empty.
		 */
		private function setError( $message ) {
			if ( ( $value = $this->processErrors( $message ) ) === null ) {
				return false;
			}

			setcookie( $this->getCookieName(), $value, 0, '/' );

			return true;
		}

		/**
		 * Process errors.
		 *
		 * @param string $message Error message.
		 *
		 * @return string Prepared list of errors in JSON format.
		 */
		private function processErrors( $message ) {
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