<?php

namespace AnyComment;

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
		'AnyComment\Admin\AnyCommentFiles',

		// Other
		'AnyComment\Admin\AnyCommentWPComments',
		'AnyComment\AnyCommentAvatars',

		// Hooks
		'AnyComment\Hooks\AnyCommentCommentHooks',
		'AnyComment\Hooks\AnyCommentNativeLoginForm',
		'AnyComment\Hooks\AnyCommentWooCommerce',

		// Crontabs
		'AnyComment\cron\AnyCommentEmailQueueCron',

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