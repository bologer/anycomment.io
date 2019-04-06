<?php

namespace AnyComment\Migrations;

/**
 * Class AnyCommentMigration_0_0_8 fixes issue when "\" was added multiple times to setting values having ' or ".
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Migrations
 */
class AnyCommentMigration_0_0_88 extends AnyCommentMigration {
	public $version = '0.0.88';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied () {
		$options = get_option( 'anycomment-generic', null );

		if ( empty( $options ) ) {
			return true;
		}

		if ( ! isset( $options['options_design_font_family'] ) ) {
			return true;
		}

		if ( strpos( $options['options_design_font_family'], '\\' ) !== false ||
		     strpos( $options['options_design_font_family'], '/' ) !== false ) {
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up () {

		$options = get_option( 'anycomment-generic', null );


		if ( empty( $options ) ) {
			return true;
		}

		if ( isset( $options['options_design_font_family'] ) ) {
			$options['options_design_font_family'] = str_replace( [
				'/',
				'\\',
			], '', $options['options_design_font_family'] );
		}

		update_option( 'anycomment-generic', $options );

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down () {
		return true;
	}
}

// eof;
