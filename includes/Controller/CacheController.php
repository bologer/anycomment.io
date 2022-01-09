<?php

namespace AnyComment\Controller;

use AnyComment\AnyCommentCore;
use AnyComment\Web\BaseController;
use AnyComment\Cache\AnyCommentCacheManager;
use AnyComment\Cache\AnyCommentRestCacheManager;

/**
 * Controls cache flushing related logic.
 *
 * @package AnyComment\Controller
 */
class CacheController extends BaseController {
	/**
	 * Handles download of debug report.
	 */
	public function actionFlush( $type ) {
		$this->verifyNonce();

		switch ( $type ) {
			case 'all':
				AnyCommentCacheManager::flushAll();
				break;
			case 'rest':
				AnyCommentRestCacheManager::flush();
				break;
			default:
		}

		// Removes old cache
		AnyCommentCore::cache()->getDriver()->purge();

		AnyCommentCore::instance()->getNotice()->success( __( 'Cache was successfully flushed!', 'anycomment' ) );

		return $this->redirect( [ 'tab' => 'tools' ] );
	}
}
