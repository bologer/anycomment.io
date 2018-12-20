<?php

namespace AnyComment\Interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Interface AnyCommentMigrationInterface used to control signature of migration models applied in the plugin.
 *
 * @package AnyComment\includes\interfaces
 */
interface  AnyCommentMigrationInterface {
	public function is_applied();

	public function up();

	public function down();
}
