<?php

namespace AnyComment\Controller;

use AnyComment\AnyCommentCore;
use AnyComment\Web\BaseController;
use AnyComment\Debug\DebugReportExport;
use AnyComment\Debug\DebugReportGenerator;

/**
 * Class DebugController processing debug related logic. For example, it processing download of debug report used
 * by AnyComment developers to investigate bugs.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Controller
 */
class DebugController extends BaseController {
	/**
	 * Handles download of debug report.
	 *
	 * Use by AnyComment developers to investigate plugin bugs.
	 */
	public function actionDownload() {
		$this->verifyNonce();
		try {
			$debug_generator = new DebugReportGenerator();
			$debug_export    = new DebugReportExport( $debug_generator );
			$debug_export->streamExport();
		} catch ( \Exception $exception ) {
			AnyCommentCore::logger()->error( sprintf(
				'Failed to execute debug/download, exception: %s',
				$exception->getMessage()
			) );
		}
	}
}
