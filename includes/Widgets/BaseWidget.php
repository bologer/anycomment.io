<?php

namespace AnyComment\Widgets;

abstract class BaseWidget {
	/**
	 * Render widget content.
	 *
	 * @param array $options List of render options.
	 * @return string
	 */
	protected abstract function render( $options = [] );

	/**
	 * Should generate CSS style relevant to
	 * @return mixed
	 */
	protected abstract function generate_styles();
}