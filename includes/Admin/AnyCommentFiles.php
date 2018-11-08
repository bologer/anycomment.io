<?php

namespace AnyComment\Admin;

use AnyComment\Helpers\AnyCommentTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AnyCommentFiles {
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initiate hooks.
	 */
	private function init_hooks() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	/**
	 * Init admin menu.
	 */
	public function add_menu() {
		add_submenu_page(
			'anycomment-dashboard',
			__( 'Files', "anycomment" ),
			__( 'Files', "anycomment" ),
			'manage_options',
			'anycomment-files',
			[ $this, 'page_files' ]
		);
	}

	/**
	 * Display dashboard page.
	 */
	public function page_files() {
		return AnyCommentTemplate::render( 'admin/files' );
	}
}