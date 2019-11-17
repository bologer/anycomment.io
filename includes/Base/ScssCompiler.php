<?php

namespace AnyComment\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\AnyCommentCore;
use Leafo\ScssPhp\Compiler;

/**
 * Class SCSSCompiler is wrapper around SCSS package compiler.
 *
 * @see Compiler for furher information.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Base
 */
class ScssCompiler {
	/**
	 * @var array List of SCSS files ot be loaded and processed.
	 */
	private $_scss;

	/**
	 * @var string Formatter.
	 */
	private $_formatter;

	/**
	 * @var string|array Import paths.
	 */
	private $_import_path;

	/**
	 * @var array|null Variables to be replaced.
	 */
	private $_variables = [];

	/**
	 * Load list of SCSS paths to be compiled.
	 *
	 * @param array|string $paths
	 *
	 * @return $this
	 */
	public function set_scss( $paths ) {
		$this->_scss = (array) $paths;

		return $this;
	}

	/**
	 * Set formatter.
	 *
	 * @param string $formatter
	 *
	 * @return $this
	 * @see Compiler::setFormatter() for further information.
	 *
	 */
	public function set_formatter( $formatter ) {
		$this->_formatter = $formatter;

		return $this;
	}

	/**
	 * Set SCSS import path.
	 *
	 * @param string $import_path
	 *
	 * @return $this
	 * @see Compiler::setImportPaths() for further information.
	 *
	 */
	public function set_import_path( $import_path ) {
		$this->_import_path = $import_path;

		return $this;
	}

	/**
	 * Set variables to be replaced.
	 *
	 * @param array $variables
	 *
	 * @return $this
	 * @see Compiler::setVariables() for further information.
	 *
	 */
	public function set_variables( $variables ) {
		$this->_variables = $variables;

		return $this;
	}

	/**
	 * Get list of SCSS to process.
	 *
	 * @return array
	 */
	public function get_scss() {
		return $this->_scss;
	}

	/**
	 * Get concat string of all specified SCSS files.
	 *
	 * @return string|null NULL on failure. String when all SCSS were concat together.
	 */
	public function prepare_scss() {
		$scss_paths = $this->get_scss();

		if ( empty( $scss_paths ) ) {
			return null;
		}

		$content = '';

		foreach ( $scss_paths as $path ) {
			if ( file_exists( $path ) && is_readable( $path ) ) {
				$file_content = file_get_contents( $path );
				if ( $file_content === false ) {
					continue;
				}

				$content .= $file_content;
			}
		}

		if ( empty( $content ) ) {
			return null;
		}

		return $content;
	}

	/**
	 * get formatter.
	 *
	 * @return string
	 */
	public function get_formatter() {
		$formatter = $this->_formatter;

		if ( empty( $formatter ) ) {
			$formatter = 'Leafo\ScssPhp\Formatter\Crunched';
		}

		return $formatter;
	}

	/**
	 * Get SCSS import path.
	 *
	 * @return array|string
	 */
	public function get_import_path() {
		return $this->_import_path;
	}

	/**
	 * Get variables to be replaced in SCSS.
	 *
	 * @return array|null
	 */
	public function get_variables() {
		return $this->_variables;
	}

	/**
	 * Compile SCSS to CSS. When save path specified,
	 * the method will try to save compiled CSS.
	 *
	 * @param string|null NULL when required to return just compiled CSS without saving it to a file.
	 *
	 * @return bool|string False on failure.
	 */
	public function compile( $save_path = null ) {
		$formatter    = $this->get_formatter();
		$import_path  = $this->get_import_path();
		$variables    = $this->get_variables();
		$scss_content = $this->prepare_scss();

		if ( $scss_content === null ) {
			AnyCommentCore::logger()->error(sprintf(
				''
			));
			return false;
		}

		$compiler = new Compiler();

		$compiler->setFormatter( $formatter );
		$compiler->setImportPaths( $import_path );
		$compiler->setVariables( $variables );

		try {
			$compiled_css = $compiler->compile( $scss_content );
		} catch ( \Exception $exception ) {
			AnyCommentCore::logger()->error( sprintf(
				'Failed to compile SCSS, exception thrown: "%s", in %s:%s',
				$exception->getMessage(),
				$exception->getFile(),
				$exception->getLine()
			) );

			return false;
		}

		if ( empty( $compiled_css ) ) {
			return false;
		}

		// When save path specified, should try to save compiled CSS there
		if ( $save_path !== null ) {
			$file_saved = file_put_contents( $save_path, $compiled_css );

			return ( false !== $file_saved );
		}

		return $compiled_css;
	}
}
