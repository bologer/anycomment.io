<?php

namespace AnyComment\Base;

/**
 * Class BaseObject is base class implementation.
 *
 * @since 0.0.99
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Base
 */
class BaseObject {
	/**
	 * @param array $config
	 */
	public function __construct( $config = [] ) {
		static::configure( $this, $config );
		$this->init();
	}

	/**
	 * Initiation class. Called by class constructor.
	 */
	public function init() {
	}

	/**
	 * Configures object to initial object values.
	 *
	 * @param $object
	 * @param $properties
	 *
	 * @return mixed
	 */
	public static function configure( $object, $properties ) {
		foreach ( $properties as $name => $value ) {
			$object->$name = $value;
		}

		return $object;
	}
}
