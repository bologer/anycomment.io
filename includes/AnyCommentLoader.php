<?php

namespace AnyComment;

/**
 * Class AnyCommentLoader is used as main plugin loader.
 *
 * Define list of classes to be automatically constructed.
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
		'AnyComment\Rest\AnyCommentRestServiceSync',
		'AnyComment\Rest\AnyCommentRestLikes',
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
		'AnyComment\Hooks\AnyCommentCommonHooks',
		'AnyComment\Hooks\AnyCommentCommentHooks',
		'AnyComment\Hooks\AnyCommentUserHooks',
		'AnyComment\Hooks\AnyCommentNativeLoginForm',

        // Libraries
        'AnyComment\Libraries\AnyCommentUserTour',

		'AnyComment\Integrations\AnyCommentWooCommerce',
		'AnyComment\Integrations\AnyCommentBuddyPress',

		// Crontabs
		'AnyComment\Cron\AnyCommentEmailQueueCron',
		'AnyComment\Cron\AnyCommentToolsCron',
		'AnyComment\Cron\AnyCommentServiceSyncCron',

		// Emails
		'AnyComment\EmailEndpoints',

		// Widgets
		'AnyComment\Widgets\Native\CommentList',

		// Main
		'AnyComment\AnyCommentRender',
	];

	/**
	 * Load list of classes to be invoked immediately.
	 */
	public static function load () {
		if ( ! empty( static::$load ) ) {
			foreach ( static::$load as $namespace ) {
				if ( class_exists( $namespace ) ) {
					new $namespace();
				}

				if ( strpos( $namespace, 'AnyComment\Widgets' ) !== false ) {
					add_action( 'widgets_init', function () use ( $namespace ) {
						register_widget( $namespace );
					} );
				}
			}
		}
	}
}
