<?php

namespace AnyComment;

/**
 * Class AnyCommentLoader is used as main plugin loader.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment
 */
class AnyCommentLoader {
	/**
	 * @var array List of classes to invoke immediately.
	 */
	public static $load = [
		// Rest
		'AnyComment\Rest\AnyCommentRestComment',
		'AnyComment\Rest\AnyCommentRestLikes',
		'AnyComment\Rest\AnyCommentRestUsers',
		'AnyComment\Rest\AnyCommentRestDocuments',
		'AnyComment\Rest\AnyCommentRestRate',
		'AnyComment\Rest\AnyCommentRestSubscriptions',
		'AnyComment\Rest\AnyCommentSocialAuth',

		// Admin
		'AnyComment\Admin\AnyCommentAdminPages',
		'AnyComment\Admin\AnyCommentStatistics',
		'AnyComment\Admin\AnyCommentFilesPage',
		'AnyComment\Admin\AnyCommentRatingPage',
		'AnyComment\Admin\AnyCommentSubscriptionsPage',
		'AnyComment\Admin\AnyCommentEmailQueuePage',

		// Other
		'AnyComment\Admin\AnyCommentWPComments',
		'AnyComment\AnyCommentAvatars',

		// Hooks
		'AnyComment\Hooks\AnyCommentCommentHooks',
		'AnyComment\Hooks\AnyCommentNativeLoginForm',

		'AnyComment\Integrations\AnyCommentWooCommerce',

		// Crontabs
		'AnyComment\cron\AnyCommentEmailQueueCron',

		// Emails
		'AnyComment\EmailEndpoints',

		// Main
		'AnyComment\AnyCommentRender',
	];

	/**
	 * Load list of classes to be invoked immediately.
	 */
	public static function load() {
		if ( ! empty( static::$load ) ) {
			foreach ( static::$load as $namespace ) {
				if ( class_exists( $namespace ) ) {
					new $namespace();
				}
			}
		}
	}
}