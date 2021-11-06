<?php

namespace AnyComment;

use AnyComment\Base\BaseObject;
use AnyComment\Models\AnyCommentSubscriptions;

/**
 * Class EmailEndpoints is used to register endpoints to handle email related actions.
 *
 * For example, cancel/confirm email subscription.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment
 * @since 0.0.70
 */
class EmailEndpoints extends BaseObject {

	const CANCEL_QUERY_PARAM = 'anycomment_cancel_subscription';
	const CONFIRM_QUERY_PARAM = 'anycomment_confirm_subscription';

	/**
	 * Init hooks.
	 */
	public function init() {
		add_filter( 'query_vars', function ( $query_vars ) {
			$query_vars[] = self::CANCEL_QUERY_PARAM;
			$query_vars[] = self::CONFIRM_QUERY_PARAM;

			return $query_vars;
		} );

		add_action( 'template_include', [ $this, 'email_actions' ] );
	}

	/**
	 * Process emails actions such as confirmation and cancellation.
	 *
	 * @param string $template Template name used to render the page.
	 *
	 * @return string
	 */
	public function email_actions( $template ) {
		$confirmation_token = get_query_var( self::CONFIRM_QUERY_PARAM );
		$cancel_token       = get_query_var( self::CANCEL_QUERY_PARAM );

		if ( ! empty( $confirmation_token ) || ! empty( $cancel_token ) ) {
			$is_cancel     = ! empty( $cancel_token );
			$working_token = $is_cancel ? $cancel_token : $confirmation_token;

			$token_model = AnyCommentSubscriptions::find_by_token( $working_token );
			if ( $token_model === null ) {
				wp_redirect( '/' );
				exit;
			}

			// Make user active/inactive depending on the action
			$action_performed = $is_cancel ?
				AnyCommentSubscriptions::mark_as_inactive_by_token( $working_token ) :
				AnyCommentSubscriptions::mark_as_active_by_token( $working_token );

			if ( $action_performed ) {

				// Generate message to the end user about successful action
				$message = $is_cancel ?
					__( "You were unsubscribed successfully. You will be redirect back to the post in a momemnt.", "anycomment" ) :
					__( "Your email confirmed. You will redirected back to post in a moment.", "anycomment" );

				// Generate post permalink to redirect user back to the post where he was subscribed
				$permalink = get_permalink( $token_model->post_ID );
				$redirect  = false === $permalink ? '/' : $permalink . '#comments';
				echo '<p>' . esc_html( $message ) . '</p>';
				header( "refresh:2;url=$redirect" );
				exit;
			}

			wp_redirect( '/' );
			exit;
		}

		return $template;
	}
}
