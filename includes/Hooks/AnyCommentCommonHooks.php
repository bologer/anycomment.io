<?php

namespace AnyComment\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Base\BaseObject;

/**
 * Class AnyCommentCommonHooks consists of common hooks.
 *
 * @since 0.0.58
 */
class AnyCommentCommonHooks extends BaseObject {
    /**
     * @inheritDoc
     */
    public function init () {
		if ( ! AnyCommentGenericSettings::is_show_admin_bar() ) {
			add_action( 'init', [ $this, 'hide_admin_bar' ] );
			add_action( 'template_redirect', [ $this, 'redirect_on_hidden_admin_bar' ], 9999 );
		}
	}

	/**
	 * Hide admin bar for users who do not have `manage_options` permission.
	 */
	public function hide_admin_bar () {

		if ( $this->should_hide_admin_bar() ) {
			add_filter( 'show_admin_bar', '__return_false', 999999 );
		}
	}

	/**
	 * Check whether admin bar should be hidden or not by checking user caps.
	 *
	 * @return bool
	 */
	public function should_hide_admin_bar () {
		return ! current_user_can( 'manage_network' ) &&
		       ! current_user_can( 'manage_options' ) &&
		       ! current_user_can( 'moderate_comments' ) &&
		       ! current_user_can( 'edit_published_posts' );
	}

	/**
	 * Redirect user to the home page on hidden admin bar option.
	 */
	public function redirect_on_hidden_admin_bar () {
		// When trying to access /wp-admin/ and user is not under editor or administrator role -> redirected home
		if ( is_admin() && $this->should_hide_admin_bar() ) {
			wp_redirect( home_url() );
			exit();
		}
	}
}
