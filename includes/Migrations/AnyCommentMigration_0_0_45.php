<?php

namespace AnyComment\Migrations;

class AnyCommentMigration_0_0_45 extends AnyCommentMigration {
	public $table = 'email_queue';
	public $version = '0.0.45';

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		global $wpdb;
		$res            = $wpdb->get_results( "SHOW TABLES LIKE 'anycomment_email_queue';", 'ARRAY_A' );
		$isTableCreated = ! empty( $res ) && count( $res ) == 1;

		return $isTableCreated;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		/**
		 * Modify VK API options
		 */
		$option = get_option( 'anycomment-social', true );

		if ( isset( $option['social_vk_toggle_field'] ) ) {
			$option['social_vkontakte_toggle_field'] = $option['social_vk_toggle_field'];
			unset( $option['social_vk_toggle_field'] );
		}

		if ( isset( $option['social_vk_app_id_field'] ) ) {
			$option['social_vkontakte_app_id_field'] = $option['social_vk_app_id_field'];
			unset( $option['social_vk_app_id_field'] );
		}

		if ( isset( $option['social_vk_app_secret_field'] ) ) {
			$option['social_vkontakte_app_secret_field'] = $option['social_vk_app_secret_field'];
			unset( $option['social_vk_app_secret_field'] );
		}

		update_option( 'anycomment-social', $option );

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table           = 'anycomment_email_queue';

		/**
		 * Create email queue table
		 */
		$sql = "CREATE TABLE `$table` (
			  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `post_ID` bigint(20) UNSIGNED DEFAULT NULL,
			  `comment_ID` bigint(20) UNSIGNED DEFAULT NULL,
			  `subject` VARCHAR(255) NOT NULL,
			  `email` VARCHAR(255) DEFAULT NULL,
			  `content` LONGTEXT COLLATE utf8_unicode_ci NOT NULL,
			  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `is_sent` BOOL DEFAULT 0,
			  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`ID`),
			  KEY `post_ID` (`post_ID`),
			  KEY `comment_ID` (`comment_ID`)
		) $charset_collate";

		return false !== $wpdb->query( $sql );
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS anycomment_email_queue;";
        $wpdb->query( $sql );

		return true;
	}
}

// eof;
