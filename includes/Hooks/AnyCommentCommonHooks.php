<?php

namespace AnyComment\Hooks;

use AnyComment\Admin\AnyCommentGenericSettings;

/**
 * Class AnyCommentCommonHooks consists of common hooks.
 *
 * @since 0.0.58
 */
class AnyCommentCommonHooks {
	public function __construct() {
		$this->init();
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		if ( ! AnyCommentGenericSettings::is_show_admin_bar() ) {
			add_action( 'init', [ $this, 'hide_admin_bar' ] );
			add_action( 'template_redirect', [ $this, 'redirect_on_hidden_admin_bar' ] );
		}
	}

	/**
	 * Hide admin bar for users who do not have `manage_options` permission.
	 */
	public function hide_admin_bar() {
		if ( ! current_user_can( 'manage_options' ) ) {
			show_admin_bar( false );
		}
	}

	/**
	 * Redirect user to the home page on hidden admin bar option.
	 */
	public function redirect_on_hidden_admin_bar() {
		// When trying to access /wp-admin/ and user cannot manage options, should be
		// redirected to home page
		if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
			wp_redirect( home_url() );
			exit();
		}
	}
}