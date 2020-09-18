<?php

namespace AnyComment\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Base\BaseObject;
use AnyComment\Helpers\AnyCommentFileHelper;

/**
 * Class AnyCommentToolsCron handles common tools cron job logic.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Cron
 */
class AnyCommentToolsCron extends BaseObject {
	/**
	 * @inheritDoc
	 */
	public function init() {
		if ( ! wp_next_scheduled( 'anycomment_tools_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'anycomment_tools_cron' );
		}

		add_action( 'anycomment_tools_cron', [ $this, 'clear_cache' ] );
	}

	/**
	 * Cron tab to remove stale cache.
	 *
	 * @return bool
	 */
	public function clear_cache() {
		return AnyCommentFileHelper::unlinkDirectory( ANYCOMMENT_CACHE_DIR );
	}
}
