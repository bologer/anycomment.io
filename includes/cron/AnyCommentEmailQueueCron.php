<?php

class AnyCommentEmailQueueCron {
	/**
	 * AnyCommentEmailCron constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init class.
	 */
	private function init() {

		add_filter( 'cron_schedules', [ $this, 'add_minute_interval' ] );

		if ( ! wp_next_scheduled( 'anycomment_email_queue_send_cron' ) ) {
			wp_schedule_event( time(), 'every_minute', 'anycomment_email_queue_send_cron' );
		}

		if ( ! wp_next_scheduled( 'anycomment_email_queue_check_cron' ) ) {
			wp_schedule_event( time(), 'every_minute', 'anycomment_email_queue_check_cron' );
		}


		add_action( 'anycomment_email_queue_send_cron', [ $this, 'send_emails' ] );
		add_action( 'anycomment_email_queue_check_cron', [ $this, 'check_for_queue' ] );
	}

	/**
	 * Add new every minute interval.
	 *
	 * @param array $schedules List of available schedules.
	 *
	 * @return mixed
	 */
	public function add_minute_interval( $schedules ) {
		$schedules['every_minute'] = array(
			'interval' => 60,
			'display'  => esc_html__( 'Every Minute' ),
		);

		return $schedules;
	}

	/**
	 * Check new comments to be added to the email queue.
	 *
	 * @return bool
	 */
	public function check_for_queue() {
		$comments = AnyCommentEmailQueue::grabToAdd();

		if ( empty( $comments ) ) {
			return false;
		}

		$addedCount = 0;

		/**
		 * @var $comment WP_Comment
		 */
		foreach ( $comments as $key => $comment ) {
			$email = new AnyCommentEmailQueue();

			$email->user_ID    = $comment->parent_user_ID;
			$email->post_ID    = $comment->comment_post_ID;
			$email->comment_ID = $comment->comment_ID;
			$email->content    = AnyCommentEmailQueue::generateReplyEmail( $email );

			$isAdded = AnyCommentEmailQueue::add( $email );

			if ( $isAdded ) {
				$addedCount ++;
			}
		}

		return count( $comments ) === $addedCount;
	}

	/**
	 * Cron tab method to send emails.
	 *
	 * @return bool
	 */
	public function send_emails() {
		$emails = AnyCommentEmailQueue::grabRepliesToSend();

		if ( empty( $emails ) ) {
			return false;
		}

		$successCount = 0;

		/**
		 * @var $email AnyCommentEmailQueue
		 */
		foreach ( $emails as $key => $email ) {
			$post = get_post( $email->post_ID );

			if ( $post !== null ) {
				$subject = sprintf( __( "Comment on %s" ), $post->post_title );
			} else {
				$subject = __( "Re: New Comment" );
			}

			$headers   = [];
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$body = AnyCommentEmailQueue::generateReplyEmail( $email );

			/**
			 * When required to notify new users about replies, them them email,
			 * otherwise fake it as sent in order not to break the logic of the queue.
			 */
			$isSent = AnyCommentGenericSettings::isNotifyOnNewReply() ?
				wp_mail( $email->notify_email, $subject, $body, $headers ) :
				true;

			if ( $isSent ) {
				AnyCommentEmailQueue::markAsSent( $email->ID );
				$successCount ++;
			}
		}

		return count( $emails ) === $successCount;
	}
}