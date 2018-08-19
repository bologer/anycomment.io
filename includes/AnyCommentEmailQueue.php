<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnyCommentEmailQueue keeps data about emails to be send to users.
 *
 * @property int $ID
 * @property int|null $user_ID
 * @property int $post_ID
 * @property int $comment_ID
 * @property string $content Content of the email.
 * @property string|null $ip
 * @property string|null $user_agent
 * @property datetime $sent_at
 * @property datetime $created_at
 *
 * @since 0.0.3
 */
class AnyCommentEmailQueue {

	public $ID;
	public $user_ID;
	public $post_ID;
	public $comment_ID;
	public $content;
	public $user_agent;
	public $ip;
	public $sent_at = '0000-00-00 00:00:00';
	public $created_at;

	/**
	 * AnyCommentEmailQueue constructor.
	 */
	public function __construct() {
		$this->created_at = current_time( 'mysql' );
	}

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function tableName() {
		return 'anycomment_email_queue';
	}

	/**
	 * @return wpdb
	 */
	public static function db() {
		global $wpdb;

		return $wpdb;
	}

	/**
	 * Get latest
	 * @return AnyCommentEmailQueue|null|object
	 */
	public static function getNewest() {
		$tableName = static::tableName();
		$sql       = "SELECT * FROM `$tableName` ORDER BY `id` DESC LIMIT 1";

		return static::db()->get_row( $sql );
	}

	/**
	 * Get fresh comments which were not notified about yet.
	 *
	 * @return WC_Comments[]|null WC_Comments[] on success, NULL on failure.
	 */
	public static function grabToAdd() {

		$wpdb             = static::db();
		$commentsTable    = $wpdb->comments;
		$usersTable       = $wpdb->users;
		$latestEmailQueue = static::getNewest();
		$latestDate       = $latestEmailQueue->created_at;

		/**
		 * Select
		 * - list of replies to parent comment
		 * - make sure user has email
		 * - make sure user did not reply to his own comment
		 * - and comment date is after the latest reply sent
		 */
		$sql = "SELECT `comments`.*, `users`.`ID` AS parent_user_ID FROM `$commentsTable` `comments` 
INNER JOIN `$commentsTable` `innerComment` ON `innerComment`.`comment_ID` = `comments`.`comment_parent` 
INNER JOIN `$usersTable` `users` ON `users`.`ID` = `innerComment`.`user_ID` 
WHERE `comments`.`comment_parent` != 0 AND `users`.`user_email` != '' AND `comments`.`user_ID` != `innerComment`.`user_ID` AND `comments`.`comment_date` > '$latestDate'";

		return $wpdb->get_results( $sql );
	}

	/**
	 * Grab emails to be sent.
	 *
	 * @return null|AnyCommentEmailQueue[]
	 */
	public static function grabRepliesToSend() {
		$usersTable = static::db()->users;
		$tableName  = static::tableName();
		$sql        = "SELECT `emails`.*, `users`.`user_email` AS notify_email FROM `$tableName` `emails` 
LEFT JOIN `$usersTable` `users` ON `emails`.`user_ID` = `users`.`ID`
WHERE `emails`.`sent_at` = '0000-00-00 00:00:00'";

		return static::db()->get_results( $sql );
	}

	/**
	 * Check whether specified email ID was already sent or not.
	 *
	 * @param int $email_id Email ID to check for.
	 *
	 * @return bool
	 */
	public static function isSent( $email_id ) {
		if ( empty( $email_id ) || ! is_numeric( $email_id ) ) {
			return false;
		}

		$tableName = static::tableName();
		$query     = static::db()->prepare( "SELECT `id`, `sent_at` FROM `$tableName` WHERE `id`=%d LIMIT 1", [ $email_id ] );

		/**
		 * @var $model AnyCommentEmailQueue|null
		 */
		$model = static::db()->get_row( $query );

		if ( empty( $model ) ) {
			return false;
		}

		if ( $model->sent_at === '0000-00-00 00:00:00' ) {
			return false;
		}

		return true;
	}

	/**
	 * Mark email as sent.
	 *
	 * Notice: when email is already sent, method will return true.
	 *
	 * @param int $email_id Email to be updated.
	 *
	 * @return bool
	 */
	public static function markAsSent( $email_id ) {
		if ( empty( $email_id ) || ! is_numeric( $email_id ) ) {
			return false;
		}

		if ( static::isSent( $email_id ) ) {
			return true;
		}

		$isUpdated = static::db()->update( static::tableName(), [ 'sent_at' => current_time( 'mysql' ) ], [ 'ID' => $email_id ] );

		return $isUpdated !== false;
	}

	/**
	 * Add new email to queue.
	 *
	 * @param AnyCommentEmailQueue $email
	 * @param string $returnOnSuccess What to return on success.
	 * - ID - will return new record ID
	 * - OBJECT - will return email queue
	 *
	 * @return bool|int|AnyCommentEmailQueue
	 */
	public static function add( $email, $returnOnSuccess = 'ID' ) {
		if ( ! $email instanceof AnyCommentEmailQueue ) {
			return false;
		}

		if ( ! isset( $email->ip ) ) {
			$email->ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
		}

		if ( ! isset( $email->user_agent ) ) {
			$email->user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
		}

		if ( ! isset( $email->ip ) ) {
			$email->ip = isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : null;
		}

		if ( ! isset( $email->created_at ) ) {
			$email->created_at = current_time( 'mysql' );
		}

		global $wpdb;

		unset( $email->ID );

		$tableName = static::tableName();
		$count     = $wpdb->insert( $tableName, (array) $email );

		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$email->ID = $lastId;

			if ( $returnOnSuccess === 'ID' ) {
				return $lastId;
			} elseif ( $returnOnSuccess === 'OBJECT' ) {
				return $email;
			}

			return true;
		}

		return false;
	}

	/**
	 * Prepare email body.
	 *
	 * @param AnyCommentEmailQueue $email
	 *
	 * @return string HTML formatted content of email.
	 */
	public static function generateReplyEmail( $email ) {
		$comment        = get_comment( $email->comment_ID );
		$post           = get_post( $email->post_ID );
		$cleanPermalink = get_permalink( $post );
		$commentLink    = sprintf( '%s#comment-%s', $cleanPermalink, $comment->comment_ID );

		$body = '<p>' . sprintf( __( 'New reply was posted in <a href="%s">%s</a>', 'anycomment' ), get_option( 'siteurl' ), get_option( 'blogname' ) ) . '</p>';

		if ( $post !== null ) {
			$body .= '<p>' . sprintf( __( 'For post <a href="%s">%s</a>', 'anycomment' ), $cleanPermalink, $post->post_title ) . '</p>';
		}

		if ( $comment !== null ) {
			$body .= '<div style="background-color:#eee; padding: 10px; font-size: 12pt; font-family: Verdana, Arial, sans-serif; line-height: 1.5;">';
			$body .= $comment->comment_content;
			$body .= '</div>';
		}

		// Add reply button
		$body .= '<p><a href="' . $commentLink . '" style="font-size: 15px;text-decoration:none;font-weight: 400;text-align: center;color: #fff;padding: 0 50px;line-height: 48px;background-color: #53af4a;display: inline-block;vertical-align: middle;border: 0;outline: 0;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;-webkit-appearance: none;-moz-appearance: none;appearance: none;white-space: nowrap;border-radius: 24px;">' . __( 'Reply', 'anycomment' ) . '</a></p>';

		return $body;
	}
}