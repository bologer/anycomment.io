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

		add_action( 'anycomment_email_queue_send_cron', [ $this, 'send_emails' ] );
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

			if ( empty( $email->email ) ) {
				AnyCommentEmailQueue::markAsSent( $email->ID );
				continue;
			}

			$headers   = [];
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$subject = $email->subject;
			$body    = $email->content;

			/**
			 * When required to notify new users about replies, them them email,
			 * otherwise fake it as sent in order not to break the logic of the queue.
			 */
			$isSent = AnyCommentGenericSettings::isNotifyOnNewReply() ?
				wp_mail( $email->email, $subject, $body, $headers ) :
				true;

			if ( $isSent ) {
				AnyCommentEmailQueue::markAsSent( $email->ID );
				$successCount ++;
			}
		}

		return count( $emails ) === $successCount;
	}
}