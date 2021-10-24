<?php

namespace AnyComment\Libraries;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Base\BaseObject;

/**
 * Class AnyCommentUserTour tracks specific GET parameter and marks specific user tour as seen.
 *
 * In addition, it has a few helpers to work with specific tours.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Libraries
 */
class AnyCommentUserTour extends BaseObject {
	/**
	 * Tracking GET param name. When available would remove notice for specific page.
	 */
	const TRACKING_PARAM_ID = 'disable-tour';

	const USER_META_KEY = 'anycomment_disabled_tour';

	/**
	 * @inheritDoc
	 */
	public function init() {
		parent::init();

		add_action( 'init', [ $this, 'track_param' ] );
	}

	/**
	 * Track GET parameter for specific parameter and if exists disables tour for current user.
	 */
	public function track_param() {
		$param_value = isset( $_GET[ self::TRACKING_PARAM_ID ] ) ? sanitize_text_field( $_GET[ self::TRACKING_PARAM_ID ] ) : null;

		if ( $param_value !== null && ! static::was_disabled( $param_value ) ) {
			static::disable( $param_value );
		}
	}


	/**
	 * Disables tour notice for current user.
	 *
	 * @param string $page
	 *
	 * @return int|false Meta ID on success, false on failure.
	 */
	public static function disable( $page ) {
		return add_user_meta(
			get_current_user_id(),
			static::get_meta_key( $page ),
			1
		);
	}

	/**
	 * Check whether notice was already disabled.
	 *
	 * @return bool
	 */
	public static function was_disabled( $page ) {
		$meta_value = get_user_meta(
			get_current_user_id(),
			static::get_meta_key( $page ),
			true
		);

		return boolval( $meta_value ) === true;
	}

	/**
	 * Build meta key to store on the user.
	 *
	 * @param string $page
	 *
	 * @return string
	 */
	public static function get_meta_key( $page ) {
		return sprintf(
			'%s_%s',
			self::USER_META_KEY,
			$page
		);
	}
}
