<?php


namespace AnyComment\Domain\Builder\Elements;

/**
 * Class Element is a base class for any elements in the builder.
 *
 * @package AnyComment\Domain\Builder\Elements
 */
abstract class Element {
	// List of supported elements.
	const ELEMENT_COMMENT_COUNTER_HEADER = 'comment_counter';
	const ELEMENT_COMMENT_FORM = 'comment_form';
	const ELEMENT_COMMENT = 'comment';

	/**
	 * @var string Component name, should be in snake case.
	 */
	public $component;

	/**
	 * @var array List of nested elements when available.
	 */
	public function getElements(): array {
		return [];
	}

	/**
	 * @var array List of options, when available.
	 */
	public function getOptions(): array {
		return [];
	}

	/**
	 * @return array
	 */
	public function getAsArray() {
		return [
			'component' => $this->component,
			'elements'  => $this->getElements(),
			'options'   => $this->getOptions()
		];
	}

	/**
	 * Get class structure as JSON.
	 *
	 * @return string
	 */
	public function __toString() {
		return json_encode( $this->getAsArray() );
	}
}
