<?php

namespace AnyComment\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AnyComment\Helpers\AnyCommentTemplate;
use AnyComment\Rest\AnyCommentSocialAuth;
use WP_Comment;

use AnyComment\EmailEndpoints;
use AnyComment\Helpers\AnyCommentRequest;
use AnyComment\Admin\AnyCommentGenericSettings;

/**
 * Class AnyCommentEmailQueue keeps data about emails to be send to users.
 *
 * @property int $ID
 * @property string|null $email
 * @property string $subject
 * @property int $post_ID
 * @property int $comment_ID
 * @property string $content Content of the email.
 * @property string|null $ip
 * @property string|null $user_agent
 * @property bool $is_sent
 * @property string $created_at
 *
 * @since 0.0.3
 */
class AnyCommentEmailQueue extends AnyCommentActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static $table_name = 'email_queue';

	public $ID;
	public $subject;
	public $post_ID;
	public $comment_ID;
	public $content;
	public $user_agent;
	public $ip;
	public $is_sent = 0;
	public $created_at;

	/**
	 * AnyCommentEmailQueue constructor.
	 */
	public function __construct () {
		$this->created_at = current_time( 'mysql' );
	}


	/**
	 * @return \wpdb
	 */
	public static function db () {
		global $wpdb;

		return $wpdb;
	}

	/**
	 * Get latest
	 * @return AnyCommentEmailQueue|null|object
	 */
	public static function get_newest () {
		$tableName = static::get_table_name();
		$sql       = "SELECT * FROM `$tableName` ORDER BY `id` DESC LIMIT 1";

		return static::db()->get_row( $sql );
	}

	/**
	 * Grab emails to be sent.
	 *
	 * @return null|AnyCommentEmailQueue[]
	 */
	public static function grab_replies_to_send () {
		$tableName = static::get_table_name();
		$sql       = "SELECT * FROM `$tableName` WHERE `is_sent` = 0";

		return static::db()->get_results( $sql );
	}

	/**
	 * Check whether specified email ID was already sent or not.
	 *
	 * @param int $email_id Email ID to check for.
	 *
	 * @return bool
	 */
	public static function is_sent ( $email_id ) {
		if ( empty( $email_id ) || ! is_numeric( $email_id ) ) {
			return false;
		}

		$tableName = static::get_table_name();
		$query     = static::db()->prepare( "SELECT `id`, `is_sent` FROM `$tableName` WHERE `id`=%d LIMIT 1", [ $email_id ] );

		/**
		 * @var $model AnyCommentEmailQueue|null
		 */
		$model = static::db()->get_row( $query );

		if ( empty( $model ) ) {
			return false;
		}

		if ( (int) $model->is_sent === 0 ) {
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
	public static function mark_as_sent ( $email_id ) {
		if ( empty( $email_id ) || ! is_numeric( $email_id ) ) {
			return false;
		}

		if ( static::is_sent( $email_id ) ) {
			return true;
		}

		$isUpdated = static::db()->update( static::get_table_name(), [ 'is_sent' => 1 ], [ 'ID' => $email_id ] );

		return $isUpdated !== false;
	}

	/**
	 * Add specified comment as reply if applicable.
	 * Method will check:
	 * - whether specified comment is child comment
	 * - not a reply to the same user
	 * - make sure parent user has email (some socials do not provide email in the API)
	 *
	 * @param WP_Comment|int $comment Instance of comment model or ID.
	 *
	 * @return AnyCommentEmailQueue|bool|int
	 */
	public static function add_as_reply ( $comment ) {

		$comment = get_comment( $comment );

		if ( empty( $comment ) || (int) $comment->comment_ID === 0 ) {
			return false;
		}

		$db            = static::db();
		$usersTable    = $db->users;
		$commentsTable = $db->comments;

		// In case when parent comment is left by registered user (social or by WordPress registration)
		$query = "SELECT `comments`.*, `users`.`user_email` AS `userTableEmail` FROM `$commentsTable` `comments` 
LEFT JOIN `$commentsTable` `innerComment` ON `innerComment`.`comment_ID` = `comments`.`comment_parent` 
LEFT JOIN `$usersTable` `users` ON `users`.`ID` = `innerComment`.`user_ID` 
WHERE `comments`.`comment_parent` != 0 
AND `users`.`user_email` != '' 
AND `comments`.`user_ID` != `innerComment`.`user_ID` 
AND `comments`.`comment_ID`=%d";


		$result = static::db()->get_row( static::db()->prepare( $query, [ $comment->comment_ID ] ) );

		// In case when parent comment is from guest
		$parentCommentEmail = get_comment_author_email( $comment->comment_parent );

		if ( empty( $result ) && empty( $parentCommentEmail ) ) {
			return false;
		}

		if ( $parentCommentEmail === $comment->comment_author_email ) {
			return false;
		}

		$model = new self();

		$post = get_post( $comment->comment_post_ID );

		if ( $post !== null ) {
			$subject = sprintf( __( "Re: Comment on %s", 'anycomment' ), $post->post_title );
		} else {
			$subject = sprintf( __( 'Re: Comment on %s', 'anycomment' ), get_option( 'blogname' ) );
		}

		$model->subject    = $subject;
		$model->email      = ! empty( $result ) ? $result->userTableEmail : $parentCommentEmail;
		$model->post_ID    = $comment->comment_post_ID;
		$model->comment_ID = $comment->comment_ID;
		$model->content    = AnyCommentEmailQueue::generate_reply_email( $model );

		return $model->save();
	}

	/**
	 * Method can be used to add email notification about new comment to admin.
	 *
	 * Notice: no email will be send if admin email from settings is matching one from comment.
	 *
	 * @param WP_Comment|int $comment Comment to be added as notification to admin.
	 *
	 * @return AnyCommentEmailQueue|bool|int
	 */
	public static function add_as_admin_notification ( $comment ) {

		$comment = get_comment( $comment );

		if ( empty( $comment ) || (int) $comment->comment_ID === 0 ) {
			return false;
		}

		$adminEmail = get_option( 'admin_email' );

		if ( $comment->comment_author_email === $adminEmail ) {
			return false;
		}

		$email = new self();

		$post = get_post( $comment->comment_post_ID );

		if ( $post !== null ) {
			$subject = sprintf( __( "New Comment on %s", 'anycomment' ), $post->post_title );
		} else {
			$subject = sprintf( __( 'New Comment on %s', 'anycomment' ), get_option( 'blogname' ) );
		}

		$email->email      = $adminEmail;
		$email->subject    = $subject;
		$email->post_ID    = $comment->comment_post_ID;
		$email->comment_ID = $comment->comment_ID;
		$email->content    = AnyCommentEmailQueue::generate_admin_email( $email );

		return $email->save();
	}

	/**
	 * Method can be used to add email notification about new comment to subscribers.
	 *
	 * @param AnyCommentSubscriptions $subscriber Subscriber email.
	 * @param WP_Comment $comment Comment to be added as notification.
	 *
	 * @return AnyCommentEmailQueue|bool|int
	 */
	public static function add_as_subscriber_notification ( $subscriber, $comment ) {
		if ( ! $comment instanceof WP_Comment ) {
			return false;
		}

		$email = new self();

		$post = get_post( $comment->comment_post_ID );

		if ( $post !== null ) {
			$subject = sprintf( __( "New Comment in %s", 'anycomment' ), $post->post_title );
		} else {
			$subject = sprintf( __( 'New Comment in %s', 'anycomment' ), get_option( 'blogname' ) );
		}

		$email->email      = $subscriber->email;
		$email->subject    = $subject;
		$email->post_ID    = $comment->comment_post_ID;
		$email->comment_ID = $comment->comment_ID;

		$email_content = AnyCommentEmailQueue::generate_subscribe_email( $subscriber, $email );

		if ( empty( $email_content ) ) {
			return false;
		}

		$email->content = $email_content;

		return $email->save();
	}

	/**
	 * Method can be used to add email confirmation for new subscribers.
	 *
	 * @param AnyCommentSubscriptions $subscriber Subscriber email.
	 *
	 * @return AnyCommentEmailQueue|bool|int
	 */
	public static function add_as_subscriber_confirmation_notification ( $subscriber ) {

		$email = new self();

		$post = get_post( $subscriber->post_ID );

		if ( $post !== null ) {
			$subject = sprintf( __( "Subscription Confirmation For %s", 'anycomment' ), $post->post_title );
		} else {
			$subject = sprintf( __( 'Subscription Confirmation For %s', 'anycomment' ), get_option( 'blogname' ) );
		}

		$email->email   = $subscriber->email;
		$email->subject = $subject;
		$email->post_ID = $subscriber->post_ID;

		/**
		 * todo: create migration to allow comment ID to be NULL
		 */
		$email->comment_ID = 0;

		$email_content = AnyCommentEmailQueue::generate_subscribe_confirmation_email( $subscriber );

		if ( empty( $email_content ) ) {
			return false;
		}

		$email->content = $email_content;

		return $email->save();
	}

	/**
	 * Save new model into email queue.
	 *
	 * @return bool
	 */
	public function save () {

		if ( ! isset( $this->ip ) ) {
			$this->ip = AnyCommentRequest::get_user_ip();
		}

		if ( ! isset( $this->user_agent ) ) {
			$this->user_agent = AnyCommentRequest::get_user_agent();
		}

		if ( ! isset( $this->created_at ) ) {
			$this->created_at = current_time( 'mysql' );
		}

		global $wpdb;

		unset( $this->ID );

		$tableName = static::get_table_name();
		$count     = $wpdb->insert( $tableName, (array) $this );

		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$this->ID = $lastId;

			return true;
		}

		return false;
	}

	/**
	 * Generate subscribe confirmation email.
	 * Subs to replace:
	 * - '{blogName}',
	 * - '{blogUrl}',
	 * - '{blogUrlHtml}',
	 * - '{postTitle}',
	 * - '{postUrl}',
	 * - '{postUrlHtml}'
	 * - '{confirmationButton}'
	 * - '{confirmationUrl}'
	 *
	 * @param AnyCommentSubscriptions $subscriber
	 *
	 * @return string
	 */
	public static function generate_subscribe_confirmation_email ( $subscriber ) {
		$token = $subscriber->token;

		$post           = get_post( $subscriber->post_ID );
		$cleanPermalink = get_permalink( $post );

		$blog_name = get_option( 'blogname' );
		$blog_url  = get_option( 'siteurl' );

		$blog_url_html = sprintf( '<a href="%s">%s</a>', $blog_url, $blog_name );

		$post_title    = '';
		$post_url      = '';
		$post_url_html = '';

		if ( $post !== null ) {
			$post_title    = $post->post_title;
			$post_url      = $cleanPermalink;
			$post_url_html = sprintf( '<a href="%s">%s</a>', $post_url, $post_title );
		}

		$confirmation_url = add_query_arg( EmailEndpoints::CONFIRM_QUERY_PARAM, $token, get_option( 'siteurl' ) );

		$confirmation_button = '<p><a href="' . $confirmation_url . '" style="font-size: 15px;text-decoration:none;font-weight: 400;text-align: center;color: #fff;padding: 0 50px;line-height: 48px;background-color: #53af4a;display: inline-block;vertical-align: middle;border: 0;outline: 0;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;-webkit-appearance: none;-moz-appearance: none;appearance: none;white-space: nowrap;border-radius: 24px;">' . __( 'Confirm', 'anycomment' ) . '</a></p>';

		$search = [
			'{blogName}',
			'{blogUrl}',
			'{blogUrlHtml}',
			'{postTitle}',
			'{postUrl}',
			'{postUrlHtml}',
			'{confirmationUrl}',
			'{confirmationButton}',
		];

		$replacement = [
			$blog_name,
			$blog_url,
			$blog_url_html,
			$post_title,
			$post_url,
			$post_url_html,
			$confirmation_url,
			$confirmation_button,
		];

		$template = AnyCommentGenericSettings::get_notify_email_subscribers_confirmation_template();

		return static::prepare_email_template( $template, $search, $replacement );
	}

	/**
	 * Prepare email body.
	 *
	 * Subs to replace:
	 * '{blogName}',
	 * '{blogUrl}',
	 * '{blogUrlHtml}',
	 * '{postTitle}',
	 * '{postUrl}',
	 * '{postUrlHtml}',
	 * '{commentText}',
	 * '{commentFormatted}',
	 * '{replyUrl}',
	 * '{replyButton}'
	 *
	 * @param AnyCommentSubscriptions $subscription
	 * @param AnyCommentEmailQueue $email
	 *
	 * @return string HTML formatted content of email.
	 */
	public static function generate_subscribe_email ( $subscription, $email ) {
		$comment        = get_comment( $email->comment_ID );
		$post           = get_post( $email->post_ID );
		$cleanPermalink = get_permalink( $post );
		$reply_url      = sprintf( '%s#comment-%s', $cleanPermalink, $comment->comment_ID );

		$blog_name = get_option( 'blogname' );
		$blog_url  = get_option( 'siteurl' );

		/**
		 * Generate unsubscribe URL
		 */
		$new_token = AnyCommentSubscriptions::refresh_token_by( [ 'ID' => $subscription->ID ] );

		if ( empty( $new_token ) ) {
			return null;
		}

		$unsubsribe_url = add_query_arg( EmailEndpoints::CANCEL_QUERY_PARAM, $new_token, $blog_url );

		$blog_url_html = sprintf( '<a href="%s">%s</a>', $blog_url, $blog_name );

		$post_title    = '';
		$post_url      = '';
		$post_url_html = '';

		$comment_text      = '';
		$comment_formatted = '';

		if ( $post !== null ) {
			$post_title    = $post->post_title;
			$post_url      = $cleanPermalink;
			$post_url_html = sprintf( '<a href="%s">%s</a>', $post_url, $post_title );
		}

		if ( $comment !== null ) {
			$comment_text = $comment->comment_content;

			$comment_formatted = '<div style="background-color:#eee; padding: 10px; font-size: 12pt; font-family: Verdana, Arial, sans-serif; line-height: 1.5;">';
			$comment_formatted .= $comment_text;
			$comment_formatted .= '</div>';
		}

		$reply_button = '<p><a href="' . $reply_url . '" style="font-size: 15px;text-decoration:none;font-weight: 400;text-align: center;color: #fff;padding: 0 50px;line-height: 48px;background-color: #53af4a;display: inline-block;vertical-align: middle;border: 0;outline: 0;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;-webkit-appearance: none;-moz-appearance: none;appearance: none;white-space: nowrap;border-radius: 24px;">' . __( 'See', 'anycomment' ) . '</a></p>';

		$search = [
			'{blogName}',
			'{blogUrl}',
			'{blogUrlHtml}',
			'{postTitle}',
			'{postUrl}',
			'{unsubscribeUrl}',
			'{postUrlHtml}',
			'{commentText}',
			'{commentFormatted}',
			'{replyUrl}',
			'{replyButton}',
		];

		$replacement = [
			$blog_name,
			$blog_url,
			$blog_url_html,
			$post_title,
			$post_url,
			$unsubsribe_url,
			$post_url_html,
			$comment_text,
			$comment_formatted,
			$reply_url,
			$reply_button,
		];

		$template = AnyCommentGenericSettings::get_notify_email_subscribers_template();

		return static::prepare_email_template( $template, $search, $replacement );
	}

	/**
	 * Prepare email body.
	 *
	 * Subs to replace:
	 * - {helloParagraph}
	 * - {afterHelloParagraph}
	 * - {commentAuthorImgSrc}
	 * - {commentAuthorName}
	 * - {commentAuthorText}
	 * - {commentAuthorDate}
	 * - {commentAuthorReplyUrl}
	 * - {commentAuthorReplyText}
	 *
	 * - {commentReplyAuthorParagraph}
	 * - {commentReplyAuthorText}
	 * - {commentReplyAuthorImgSrc}
	 *
	 * @param AnyCommentEmailQueue $email
	 *
	 * @return string HTML formatted content of email.
	 */
	public static function generate_reply_email ( $email ) {
		/**
		 * - {helloParagraph}
		 * - {afterHelloParagraph}
		 * - {commentAuthorImgSrc}
		 * - {commentAuthorName}
		 * - {commentAuthorText}
		 * - {commentAuthorDate}
		 * - {commentAuthorReplyUrl}
		 * - {commentAuthorReplyText}
		 *
		 * - {commentReplyAuthorParagraph}
		 * - {commentReplyAuthorText}
		 * - {commentReplyAuthorImgSrc}
		 */
		$comment        = get_comment( $email->comment_ID );
		$post           = get_post( $email->post_ID );
		$cleanPermalink = get_permalink( $post );

		$blog_name     = get_option( 'blogname' );
		$blog_url      = get_option( 'siteurl' );
		$blog_url_html = sprintf( '<a href="%s">%s</a>', $blog_url, $blog_name );

		$post_url_html = '';

		if ( $post !== null ) {
			$post_title    = $post->post_title;
			$post_url      = $cleanPermalink;
			$post_url_html = sprintf( '<a href="%s">%s</a>', $post_url, $post_title );
		}
		$comment_author_img_src = AnyCommentSocialAuth::get_user_avatar_url( $comment->comment_author_email );

		$comment_author_name = $comment->comment_author;
		$comment_author_text = wp_trim_words( $comment->comment_content, 15 );

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		$comment_author_date = date( $date_format . ", " . $time_format );

		$comment_author_reply_url = sprintf( '%s#comment-%s', $cleanPermalink, $comment->comment_ID );

		$reply_comment = get_comment( $comment->comment_parent );

		$comment_reply_author_img_src = '';
		$comment_reply_author_text    = '';

		$reply_comment_user = null;
		if ( ! empty( $reply_comment ) ) {
			$comment_reply_author_img_src = AnyCommentSocialAuth::get_user_avatar_url( $reply_comment->comment_author_email );
			$comment_reply_author_text    = wp_trim_words( $reply_comment->comment_content, 15 );

			$reply_comment_user = get_user_by( 'email', $reply_comment->comment_author_email );
		}

		$search = [
			'{helloParagraph}',
			'{afterHelloParagraph}',
			'{commentAuthorImgSrc}',
			'{commentAuthorName}',
			'{commentAuthorText}',
			'{commentAuthorDate}',
			'{commentAuthorReplyUrl}',
			'{commentAuthorReplyText}',
			'{commentReplyAuthorParagraph}',
			'{commentReplyAuthorText}',
			'{commentReplyAuthorImgSrc}',
		];

		$reply_name = '';

		if ( $reply_comment_user instanceof \WP_User ) {
			if ( ! empty( $reply_comment_user->first_name ) ) {
				$reply_name = $reply_comment_user->first_name;
			} elseif ( ! empty( $reply_comment_user->user_login ) ) {
				$reply_name = $reply_comment_user->user_login;
			} else {
				$reply_name = $reply_comment_user->display_name;
			}
		}

		$replacement = [
			sprintf( __( 'Hello %s,', 'anycomment' ), $reply_name ),
			sprintf( __( 'New reply for %s on %s.', 'anycomment' ), $post_url_html, $blog_url_html ),
			$comment_author_img_src,
			$comment_author_name,
			$comment_author_text,
			$comment_author_date,
			$comment_author_reply_url,
			__( 'Reply', 'anycomment' ),
			sprintf( __( '%s replied to the following:', 'anycomment' ), $comment_author_name ),
			$comment_reply_author_text,
			$comment_reply_author_img_src,
		];

		$template = AnyCommentTemplate::render( ANYCOMMENT_ABSPATH . '/templates/emails/reply.html' );

		return static::prepare_email_template( $template, $search, $replacement );
	}

	/**
	 * Generate email template to send notification to admin.
	 *
	 * Subs to replace:
	 * '{blogName}',
	 * '{blogUrl}',
	 * '{blogUrlHtml}',
	 * '{postTitle}',
	 * '{postUrl}',
	 * '{postUrlHtml}',
	 * '{commentText}',
	 * '{commentFormatted}',
	 * '{replyUrl}',
	 * '{replyButton}'
	 *
	 * @param AnyCommentEmailQueue $email
	 *
	 * @return string
	 * todo: need rewrite as duplicate code of email template above, for now good enought
	 */
	public static function generate_admin_email ( $email ) {
		$comment        = get_comment( $email->comment_ID );
		$post           = get_post( $email->post_ID );
		$cleanPermalink = get_permalink( $post );

		$blog_name     = get_option( 'blogname' );
		$blog_url      = get_option( 'siteurl' );
		$blog_url_html = sprintf( '<a href="%s">%s</a>', $blog_url, $blog_name );

		$post_url_html = '';

		$admin_edit_url = esc_url( admin_url( 'comment.php?action=editcomment&c=' . $comment->comment_ID ) );

		if ( $post !== null ) {
			$post_title    = $post->post_title;
			$post_url      = $cleanPermalink;
			$post_url_html = sprintf( '<a href="%s">%s</a>', $post_url, $post_title );
		}

		$comment_author_img_src = AnyCommentSocialAuth::get_user_avatar_url( $comment->comment_author_email );

		$comment_author_name = $comment->comment_author;
		$comment_author_text = $comment->comment_content;

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		$comment_author_date = date( $date_format . ", " . $time_format );

		$search = [
			'{helloParagraph}',
			'{afterHelloParagraph}',
			'{commentAuthorImgSrc}',
			'{commentAuthorName}',
			'{commentAuthorText}',
			'{commentAuthorDate}',
			'{commentAuthorModerateUrl}',
			'{commentAuthorModerateText}',
		];

		$replacement = [
			__( 'Hello,', 'anycomment' ),
			sprintf( __( 'New comment for %s on %s.', 'anycomment' ), $post_url_html, $blog_url_html ),
			$comment_author_img_src,
			$comment_author_name,
			$comment_author_text,
			$comment_author_date,
			$admin_edit_url,
			__( 'Moderate', 'anycomment' ),
		];

		$template = AnyCommentTemplate::render( ANYCOMMENT_ABSPATH . '/templates/emails/admin.html' );

		return static::prepare_email_template( $template, $search, $replacement );
	}

	/**
	 * Prepare & clean email template based on provided values.
	 *
	 * @param string $content Content of the email. Could contain prepared subst. keys to be replaced.
	 * @param array $search List of keys to be replaced.
	 * @param array $replacement Replacements for keys.
	 *
	 * @return string
	 */
	public static function prepare_email_template ( $content, $search, $replacement ) {
		$content = str_replace( $search, $replacement, $content );

		$content = preg_replace( '/\{.*?\}/', '', $content );

		return apply_filters( 'anycomment_prepare_email_template', $content, $content, $search, $replacement );
	}
}
