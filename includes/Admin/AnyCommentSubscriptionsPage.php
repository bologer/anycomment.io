<?php

namespace AnyComment\Admin;

use AnyComment\Base\BaseObject;
use AnyComment\Helpers\AnyCommentTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentSubscriptionsPage is used to render list subscriptions page.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Admin
 * @since 0.0.70
 */
class AnyCommentSubscriptionsPage extends BaseObject {

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
			__( 'Subscriptions', "anycomment" ),
			__( 'Subscriptions', "anycomment" ),
			'manage_options',
			'anycomment-subscriptions',
			[ $this, 'page_html' ]
		);
	}

	/**
	 * Display uploaded files page.
	 */
	public function page_html() {
		echo AnyCommentTemplate::render( 'admin/tables/subscriptions' );
	}
}
