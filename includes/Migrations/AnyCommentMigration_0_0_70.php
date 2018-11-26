<?php

namespace AnyComment\Migrations;

use AnyComment\AnyCommentUser;

/**
 * Class AnyCommentMigration_0_0_70 is used to migrate usernames such as "social_username" to
 * more user friendly way.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Migrations
 */
class AnyCommentMigration_0_0_70 extends AnyCommentMigration {
	public $version = '0.0.70';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		return version_compare( get_option( 'anycomment_migration' ), $this->version, '>=' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;


		$socials = [
			'vkontakte_',
			'twitter_',
			'facebook_',
			'google_',
			'github_',
			'odnoklassniki_',
			'instagram_',
			'twitch_',
			'dribbble_',
			'yahoo_',
			'wordpress_'
		];

		$regexp = implode( '|', $socials );

		$sql = "SELECT * FROM {$wpdb->users} WHERE user_login REGEXP '$regexp'";

		$users = $wpdb->get_results( $sql );

		if ( empty( $users ) ) {
			return true;
		}

		$users_processed = 0;

		/**
		 * @var \WP_User $user
		 */
		foreach ( $users as $key => $user ) {

			$is_meta_added = add_user_meta( $user->ID, 'anycomment_social_original_username', $user->user_login );

			if ( false !== $is_meta_added ) {
				$rows_affected = $wpdb->update( $wpdb->users, [ 'user_login' => AnyCommentUser::prepare_login( $user->display_name ) ], [ 'ID' => $user->ID ] );
				if ( $rows_affected >= 1 ) {
					$users_processed ++;
				}
			}
		}

		return $users_processed === count( $users );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		return true;
	}
}

// eof;
