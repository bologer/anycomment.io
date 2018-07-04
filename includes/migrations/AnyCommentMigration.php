<?php

interface  AnyCommentMigrationInterface {
	public function isApplied();

	public function up();

	public function down();
}

class AnyCommentMigration implements AnyCommentMigrationInterface {
	public $prefix = 'anycomment_';

	public $table = null;

	/**
	 * @var array List of migration to apply. Top is the most recent.
	 */
	private $_list = [
		'0021',
	];

	public function isApplied() {
		return true;
	}

	public function up() {
		return true;
	}

	public function down() {
		return true;
	}

	/**
	 * Get table name.
	 * @return null|string
	 */
	protected function getTable() {
		if ( $this->table === null ) {
			return null;
		}

		return sprintf( "%s%s", $this->prefix, $this->table );
	}

	public function getList() {
		return array_reverse( $this->_list );
	}
}