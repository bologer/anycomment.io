<?php

namespace AnyComment\Admin;

use AnyComment\Base\BaseObject;
use AnyComment\Helpers\AnyCommentTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AnyCommentRatingPage extends BaseObject {
	/**
	 * Initiate hooks.
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	/**
	 * Init admin menu.
	 */
	public function add_menu() {
		add_submenu_page(
			'anycomment-dashboard',
			__( 'Rating', "anycomment" ),
			__( 'Rating', "anycomment" ),
			'manage_options',
			'anycomment-rating',
			[ $this, 'page_html' ]
		);
	}

	/**
	 * Display uploaded files page.
	 */
	public function page_html() {
		echo AnyCommentTemplate::render( 'admin/tables/rating' );
	}
}
