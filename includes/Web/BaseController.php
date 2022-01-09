<?php

namespace AnyComment\Web;

use AnyComment\Helpers\Url;
use AnyComment\AnyCommentCore;
use AnyComment\Helpers\AnyCommentInflector;

/**
 * Class BaseController is a base controller which handles web requests.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Web
 */
class BaseController {
	/**
	 * @var string Action name which would be transformed into method name in form of 'actionActionName'.
	 */
	public $action;


	/**
	 * BaseController constructor.
	 *
	 * @param $action
	 */
	public function __construct( $action ) {
		$this->action = $action;
	}

	/**
	 * Core functions, which invokes action resolving.
	 */
	public function run() {
		$this->resolve_action();
	}

	/**
	 * Redirects user to provided URL.
	 *
	 * @param array|string $url Array list of GET parameters to append
	 * @param int $status
	 *
	 * @return bool
	 */
	public function redirect( $url, $status = 302 ) {
		$location = Url::generateUrl( $url );

		return wp_redirect( $location, $status );
	}

	/**
	 * Trying to resolve action within controller.
	 */
	protected function resolve_action() {
		$action_function = $this->get_action_method();
		$this->callAction( $action_function );
	}

	/**
	 * Call passed down function name on the current class.
	 *
	 * @param string $action_function Function name to be executed on current class.
	 *
	 * @return mixed the function result, or false on error.
	 */
	protected function callAction( $action_function ) {
		return call_user_func_array( [ $this, $action_function ], $this->get_action_parameters( $action_function ) );
	}

	/**
	 * Trying to get action parameters.
	 *
	 * It reflects current class, get method and trying to get its parameter names.
	 *
	 * @param $action_function
	 *
	 * @return array
	 */
	protected function get_action_parameters( $action_function ) {
		$action_function_parameters = [];

		try {
			$ref = new \ReflectionClass( $this );

			$get_params = AnyCommentCore::instance()->getRequest()->get();

			$functionParameters = $ref->getMethod( $action_function )->getParameters();
			foreach ( $functionParameters as $parameter ) {
				$name = $parameter->getName();

				$value         = null;
				$default_value = null;

				try {
					$default_value = $parameter->getDefaultValue();
				} catch ( \ReflectionException $exception ) {
				}

				if ( isset( $get_params[ $name ] ) ) {
					$value = is_bool( $default_value ) ? boolval( $get_params[ $name ] ) : $get_params[ $name ];
				} else {
					$value = $default_value;
				}

				$action_function_parameters[ $name ] = $value;
			}
		} catch ( \Exception $exception ) {
			AnyCommentCore::logger()->error( sprintf(
				'Failed to resolve route with action function %s, exception message: %s',
				$action_function,
				$exception->getMessage()
			) );
		}

		return $action_function_parameters;
	}

	/**
	 * Generates action name.
	 *
	 * @param string $action Action name.
	 *
	 * @return string
	 */
	protected function get_action_method() {
		return 'action' . AnyCommentInflector::pascalize( $this->action );
	}

	protected function verifyNonce($nonceKey = '_wpnonce') {
		$nonce = null;
		if ( array_key_exists( $nonceKey, $_POST ) ) {
			$nonce = sanitize_text_field( $_POST[$nonceKey] );
		} elseif ( array_key_exists( $nonceKey, $_GET ) ) {
			$nonce = sanitize_text_field( $_GET[$nonceKey] );
		}

		if ( ! $nonce || ! wp_verify_nonce( $nonce ) ) {
			wp_die( 'Nonce verification failed.' );
		}
	}
}
