<?php

/**
 * Class AnyCommentInputHelper is used to help clean inputs from unwanted data.
 *
 * @since 0.0.52
 */
class AnyCommentInputHelper {

	/**
	 * Check input and cleans from unused information.
	 *
	 * @param string $value Size. e.g. 15px, 15em or 15pt.
	 *
	 * @return string
	 */
	public static function getSizeForCss( $value ) {
		$value = strtolower( $value );

		// Clean
		$value = preg_replace( '/(?!([.0-9pt]|[.0-9px]|[.0-9em])).*?$/', '', $value );

		if ( strpos( $value, 'px' ) === false &&
		     strpos( $value, 'pt' ) === false &&
		     strpos( $value, 'em' ) === false &&
		     strpos( $value, '%' ) === false ) {
			return $value . 'px';
		}

		return strtolower( $value );
	}

	/**
	 * Get clean version of HEX color format.
	 *
	 * @param string $value e.g. #ffffff
	 *
	 * @return string
	 */
	public static function getHexForCss( $value ) {
		$value = strtolower( $value );

		$value = preg_replace( '/[g-z]/', '', $value );

		return trim( $value );
	}
}