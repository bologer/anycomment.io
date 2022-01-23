<?php

namespace AnyComment\Options;

/**
 * Class AnyCommentSection is used to build section.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Options
 */
class AnyCommentSection {
	/**
	 * @var string Unique id.
	 */
	public $id;

	/**
	 * @var string Section title.
	 */
	public $title;

	/**
	 * @var string Section description.
	 */
	public $description;

	/**
	 * @var string|callable Custom content after description.
	 */
	public $callback;

	/**
	 * @var bool Whether section should be visible or not.
	 */
	public $visible = true;

	/**
	 * @var string Section wrapping element.
	 */
	public $wrapper = '<div id="{id}" class="woption-section">{content}</div>';

	/**
	 * @var null|AnyCommentField[]
	 */
	public $fields = null;

	/**
	 * AnyCommentSection constructor.
	 *
	 * @param array $options Associative list of options to set object properties.
	 */
	public function __construct( array $options = [] ) {
		if ( ! empty( $options ) ) {
			foreach ( $options as $key => $value ) {
				if ( property_exists( $this, $key ) ) {
					$this->$key = $value;
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @return $this
	 */
	public function set_id( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return $this
	 */
	public function set_title( $title ) {
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @param string|\Closure $description
	 *
	 * @return $this
	 */
	public function set_description( $description ) {

		if ( is_callable( $description ) ) {
			$description = call_user_func( $description );
		}

		$this->description = $description;

		return $this;
	}

	/**
	 * @return callable|string
	 */
	public function get_callback() {
		return $this->callback;
	}

	/**
	 * @param callable|string $callback
	 *
	 * @return  $this
	 */
	public function set_callback( $callback ) {

		if ( is_callable( $callback ) ) {
			$this->callback .= call_user_func( $callback );
		} else {
			$this->callback .= $callback;
		}

		return $this;
	}

	/**
	 * @return AnyCommentField[]|null
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * @param AnyCommentField[]|null $fields
	 *
	 * @return $this
	 */
	public function set_fields( $fields ) {
		$this->fields = $fields;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_wrapper() {
		return $this->wrapper;
	}

	/**
	 * @param string $wrapper
	 *
	 * @return $this
	 */
	public function set_wrapper( $wrapper ) {
		$this->wrapper = $wrapper;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_visible() {
		return $this->visible;
	}

	/**
	 * @param bool $visible
	 * @param $this
	 */
	public function set_visible( $visible ) {
		$this->visible = $visible;

		return $this;
	}


	/**
	 * Convert object to string.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->run();
	}

	/**
	 * Render section to HTML.
	 *
	 * @return string
	 */
	public function run() {
		$html = '';

		$title       = $this->get_title();
		$description = $this->get_description();

		$html .= '<h2>' . $title . '</h2>';

		if ( ! empty( $description ) ) {
			$html .= '<p>' . $description . '</p>';
		}

		$html .= $this->get_callback();

		$fields = $this->get_fields();

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$html .= $field;
			}
		}

		$html = '<div style="display: ' . ($this->is_visible() ? 'block' : 'none') . ';">' . $html . '</div>';

		return str_replace( [ '{id}', '{content}' ], [ $this->get_id(), $html ], $this->wrapper );
	}
}
