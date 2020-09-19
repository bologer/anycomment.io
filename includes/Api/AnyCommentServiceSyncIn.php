<?php

namespace AnyComment\Api;

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\AnyCommentCore;
use AnyComment\AnyCommentServiceApi;
use AnyComment\AnyCommentUploadHandler;
use AnyComment\Rest\AnyCommentSocialAuth;

/**
 * Class AnyCommentServiceSyncIn helps to sync comments from service to website.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Api
 */
class AnyCommentServiceSyncIn {

	/**
	 * Processing synchronization.
	 *
	 * @return bool
	 */
	public function sync() {
		$log = AnyCommentCore::logger();

		$comment_date = static::getCommentDate();

		$log->info( 'Trying to fetch comments from cloud from date ' . $comment_date );

		$response = $this->request_comments( $comment_date );

		if ( empty( $response ) ) {
			$log->info( 'Service returned empty response, skipping sync...' );

			return false;
		}

		return $this->process_response( $response );
	}

	/**
	 *
	 * @param string $comment_date
	 *
	 * @return boolean|array
	 */
	protected function request_comments( $comment_date ) {
		$resp = AnyCommentServiceApi::request()->get( 'client/comment', [
			'token'        => AnyCommentServiceApi::getSyncApiKey(),
			'created_date' => $comment_date
		] );

		if ( is_wp_error( $resp ) ) {
			return false;
		}

		if ( ! isset( $resp['response']['code'] ) ) {
			return false;
		}

		if ( (int) $resp['response']['code'] === 200 ) {
			$jsonString = wp_remote_retrieve_body( $resp );
			$data       = json_decode( $jsonString, true );

			if ( ! isset( $data['items'] ) ) {
				return false;
			}

			return $data;
		}

		return false;
	}

	/**
	 * Processing response from service API.
	 *
	 * @param array $response
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function process_response( $response ) {
		$items = isset( $response['items'] ) && ! empty( $response['items'] ) ? $response['items'] : null;
		$users = isset( $response['users'] ) ? $response['users'] : null;
		$pages = isset( $response['pages'] ) ? $response['pages'] : null;


		$log = AnyCommentCore::logger();

		$itemsCount = is_array( $items ) ? count( $items ) : 0;
		$usersCount = is_array( $users ) ? count( $users ) : 0;
		$pagesCount = is_array( $pages ) ? count( $pages ) : 0;
		$log->info( "Processing API response: $itemsCount comment(s), $usersCount user(s) and $pagesCount page(s)" );

		if ( empty( $items ) ) {
			$log->info( "Empty list of comments returned, skipping..." );

			return true;
		}

		foreach ( $items as $key => $comment ) {
			$this->process_comment( $comment, $users, $pages );
		}

		return true;
	}

	/**
	 * Process single comment.
	 *
	 * @param array $comment Single comment to process.
	 * @param array $users List of comments.
	 * @param array $pages List of pages.
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function process_comment( $comment, $users, $pages ) {

		$log = AnyCommentCore::logger();

		$log->info( "Processing single comment from service: #{$comment['id']}" );

		$date_format      = 'Y-m-d H:i:s';
		$comment_date_utc = new \DateTime( $comment['created_date'], new \DateTimeZone( "UTC" ) );
		$comment_date     = $comment_date_utc->format( $date_format );

		if ( empty( $comment_date ) ) {
			$log->error( "Unable to convert comment's date", $comment );

			return false;
		}

		$importMeta          = isset( $comment['import_meta'] ) ? $comment['import_meta'] : null;
		$isOurSlugImportMeta = strpos( $importMeta, 'api:' ) === 0;
		if ( $isOurSlugImportMeta ) {
			$log->info( "Skipping comment #{$comment['id']} as it is already synced to service", $comment );
			static::updateCommentDateOption( $comment_date );

			return false;
		}

		$metaParentItem = static::findByImportedMeta( $comment['id'] );
		if ( ! empty( $metaParentItem ) ) {
			$log->info( "Skipping comment #{$comment['id']} as it is already imported from service", $comment );
			static::updateCommentDateOption( $comment_date );

			return false;
		}

		$author = isset( $users[ $comment['author_id'] ] ) ? $users[ $comment['author_id'] ] : null;
		$page   = isset( $pages[ $comment['website_page_id'] ] ) ? $pages[ $comment['website_page_id'] ] : null;


		if ( empty( $author ) || empty( $page ) || empty( $page['url'] ) ) {
			$log->error( "Comment #{$comment['id']} seems to have no author, page or page URL", $comment );
			static::updateCommentDateOption( $comment_date );

			return false;
		}

		$post_url = isset( $page['url'] ) ? $page['url'] : null;

		$post_id = (int) url_to_postid( $post_url );

		if ( $post_id === 0 ) {
			$log->error( "Unable to find post in local database by URL: {$post_url} for {$comment['id']}", $comment );
			static::updateCommentDateOption( $comment_date );

			return false;
		}

		if ( isset( $author['email'] ) && ! empty( $author['email'] ) ) {

			$log->debug( "Now trying to prepare author {$author['email']}" );

			$email = $author['email'];

			$user = get_user_by( 'email', $email );

			if ( $user === false ) {
				$author_date_utc = new \DateTime( $author['created_date'], new \DateTimeZone( "UTC" ) );
				$author_date     = $author_date_utc->format( $date_format );

				$userdata = [
					'first_name'      => isset( $author['first_name'] ) ? $author['first_name'] : '',
					'last_name'       => isset( $author['last_name'] ) ? $author['last_name'] : '',
					'nickname'        => isset( $author['username'] ) ? $author['username'] : '',
					'user_email'      => $email,
					'user_url'        => isset( $author['social_url'] ) ? $author['social_url'] : '',
					'role'            => AnyCommentGenericSettings::get_register_default_group(),
					'description'     => isset( $author['description'] ) ? $author['description'] : '',
					'user_registered' => $author_date,
				];

				$log->debug( "Prepared author for local database", $userdata );

				$new_user_id = wp_insert_user( $userdata );

				if ( is_integer( $new_user_id ) ) {

					$social_avatar = isset( $author['avatar_url'] ) ? $author['avatar_url'] : null;

					$usermeta = [
						AnyCommentSocialAuth::META_SOCIAL_LINK          => isset( $author['social_url'] ) ? $author['social_url'] : '',
						AnyCommentSocialAuth::META_SOCIAL_AVATAR_ORIGIN => $social_avatar,
						AnyCommentSocialAuth::META_SOCIAL_TYPE          => isset( $author['account_type'] ) ? $author['account_type'] : null,
					];

					if ( ! empty( $social_avatar ) ) {
						$log->debug( "Trying to upload avatar {$social_avatar}" );

						$serving_url = AnyCommentUploadHandler::upload_avatar( $social_avatar, $userdata );

						if ( ! empty( $serving_url ) ) {
							$log->debug( "Avatar uploaded successfully, now can served as {$serving_url}" );
							$usermeta[ AnyCommentSocialAuth::META_SOCIAL_AVATAR ] = $serving_url;
						} else {
							$log->error( "Failed to upload avatr {$social_avatar} locally, skipping it..." );
						}
					}

					$log->info( "Collected user meta for comment #{$comment['id']}", $usermeta );

					foreach ( $usermeta as $key => $value ) {
						add_user_meta( $new_user_id, $key, $value, false );
					}
				}
			}
		}

		$content = isset( $comment['content'] ) ? $comment['content'] : null;

		$wp_comment = [
			'comment_content' => $content,
			'comment_date'    => $comment_date
		];

		if ( ! empty( $author ) ) {
			$wp_comment['comment_author']       = isset( $author['name'] ) ? $author['name'] : '';
			$wp_comment['comment_author_email'] = isset( $author['email'] ) ? $author['email'] : '';
			$wp_comment['comment_author_url']   = isset( $author['social_url'] ) ? $author['social_url'] : '';

			if ( isset( $new_user_id ) && is_integer( $new_user_id ) ) {
				$wp_comment['user_id'] = $new_user_id;
			}
		}

		$wp_comment['comment_post_ID'] = $post_id;

		if ( isset( $comment['parent_id'] ) && ! empty( $comment['parent_id'] ) ) {
			$metaParentItem = static::findByImportedMeta( $comment['parent_id'] );

			if ( ! empty( $metaParentItem ) ) {
				$wp_comment['comment_parent'] = $metaParentItem['comment_id'];
			}
		}

		$imported_meta_key          = static::getCommentImportedMetaKey();
		$wp_comment['comment_meta'] = [
			$imported_meta_key => $comment['id']
		];

		$new_comment_id = wp_insert_comment( $wp_comment );

		$is_success = is_integer( $new_comment_id );

		if ( $is_success ) {
			$log->info( "Imported new comment", $wp_comment );

			static::updateCommentDateOption( $comment_date );
		} else {
			$log->error( "Failed to insert comment #{$comment['id']}", $wp_comment );
		}

		return $is_success;
	}

	/**
	 * Find comment by meta.
	 *
	 * When comment in meta, it means it was already imported into the system.
	 *
	 * @param int $commentId Comment id to check.
	 */
	public static function findByImportedMeta( $commentId ) {
		global $wpdb;

		$sql      = "SELECT * FROM $wpdb->commentmeta WHERE meta_key = %s AND meta_value = %s";
		$prepared = $wpdb->prepare( $sql, [ static::getCommentImportedMetaKey(), $commentId ] );
		$res      = $wpdb->get_results( $prepared, ARRAY_A );

		if ( ! isset( $res[0] ) ) {
			return null;
		}

		return $res[0];
	}


	/**
	 * Update comment date option.
	 *
	 * @param string $date Date in format: Y-m-d H:i:s.
	 *
	 * @return bool
	 */
	public static function updateCommentDateOption( $date ) {
		$option_name = static::getCommentDateOptionName();

		return update_option( $option_name, $date );
	}

	/**
	 * Get's latest date from option or selects one from comments table (e.g. on the first sync).
	 *
	 * @return string|null
	 */
	public static function getCommentDate() {
		$option_name  = static::getCommentDateOptionName();
		$option_value = get_option( $option_name, null );

		$log = AnyCommentCore::logger();

		$log->debug( "Import comment date is {$option_value} (option name: {$option_name})" );

		if ( empty( $option_value ) ) {

			$defaultDate = '1970-01-01 00:59:59';

			$log->debug( "Import comment date is empty, using default one $defaultDate" );

			update_option( $option_name, $defaultDate );

			return $defaultDate;
		}

		$log->debug( "Option for import comment date was previously set, value is {$option_value}, using it now" );

		return $option_value;
	}


	/**
	 * @return string
	 */
	public static function getCommentImportedMetaKey() {
		return 'anycomment_imported_service_id';
	}

	/**
	 * @return string
	 */
	public static function getCommentDateOptionName() {
		return 'anycomment_sync_last_comment_date';
	}
}
