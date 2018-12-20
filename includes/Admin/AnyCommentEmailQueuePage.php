<?php

namespace AnyComment\Admin;

use AnyComment\Helpers\AnyCommentTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AnyCommentEmailQueuePage {
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
			__( 'Emails', "anycomment" ),
			__( 'Emails', "anycomment" ),
			'manage_options',
			'anycomment-emails',
			[ $this, 'page_html' ]
		);
	}

	/**
	 * Display uploaded files page.
	 */
	public function page_html() {
		echo AnyCommentTemplate::render( 'admin/tables/emails' );
	}
}
