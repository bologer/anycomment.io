<?php

namespace AnyComment\Interfaces;

/**
 * Interface AnyCommentMigrationInterface used to control signature of migration models applied in the plugin.
 *
 * @package AnyComment\includes\interfaces
 */
interface  AnyCommentMigrationInterface {
	public function isApplied();

	public function up();

	public function down();
}