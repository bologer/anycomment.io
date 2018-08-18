<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/10/18
 * Time: 6:29 PM
 */

class AnyCommentMigration_0_0_45 extends AnyCommentMigration {
	public $table = 'email_queue';
	public $version = '0.0.45';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {

		$option = get_option( 'anycomment-social', true );

		if ( $option === true ) {
			return true;
		}

		$isVKApplied = ! isset( $option['social_vk_toggle_field'] ) &&
		               ! isset( $option['social_vk_app_id_field'] ) &&
		               ! isset( $option['social_vk_app_secret_field'] );

		global $wpdb;
		$res            = $wpdb->get_results( "SHOW TABLES LIKE '{$this->getTable()}';", 'ARRAY_A' );
		$isTableCreated = $res === null || count( $res ) > 0;

		return $isVKApplied && $isTableCreated;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		$isEmailQueueTableCreated = false;

		/**
		 * Modify VK API options
		 */
		$option = get_option( 'anycomment-social', true );

		$option['social_vkontakte_toggle_field']     = $option['social_vk_toggle_field'];
		$option['social_vkontakte_app_id_field']     = $option['social_vk_app_id_field'];
		$option['social_vkontakte_app_secret_field'] = $option['social_vk_app_secret_field'];

		unset( $option['social_vk_toggle_field'] );
		unset( $option['social_vk_app_id_field'] );
		unset( $option['social_vk_app_secret_field'] );

		$isVkUpdated = update_option( 'anycomment-social', $option );

		global $wpdb;

		$table = $this->getTable();

		/**
		 * Create email queue table
		 */
		$sql = "CREATE TABLE `$table` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_ID` bigint(20) UNSIGNED NOT NULL,
  `post_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `content` LONGTEXT COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";

		if ( $wpdb->query( $sql ) !== false ) {

			$user_id_index    = "ALTER TABLE `$table` ADD INDEX `user_ID` (`user_ID`)";
			$post_id_index    = "ALTER TABLE `$table` ADD INDEX `post_ID` (`post_ID`)";
			$comment_id_index = "ALTER TABLE `$table` ADD INDEX `comment_ID` (`comment_ID`)";

			$isEmailQueueTableCreated = $wpdb->query( $user_id_index ) !== false &&
			                            $wpdb->query( $post_id_index ) !== false &&
			                            $wpdb->query( $comment_id_index ) !== false;
		}

		$isInserted = $wpdb->insert( $table, [
			'user_ID'    => 0,
			'post_ID'    => 0,
			'comment_ID' => 0,
			'content'    => 'initial test mail',
			'ip'         => null,
			'user_agent' => null,
			'sent_at'    => current_time( 'mysql' ),
			'created_at' => current_time( 'mysql' ),
		] );

		return $isVkUpdated && $isEmailQueueTableCreated && $isInserted;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		$option = get_option( 'anycomment-social' );

		$option['social_vk_toggle_field']     = $option['social_vkontakte_toggle_field'];
		$option['social_vk_app_id_field']     = $option['social_vkontakte_app_id_field'];
		$option['social_vk_app_secret_field'] = $option['social_vkontakte_app_secret_field'];

		unset( $option['social_vkontakte_toggle_field'] );
		unset( $option['social_vkontakte_app_id_field'] );
		unset( $option['social_vkontakte_app_secret_field'] );

		$isVkOptionUpdated = update_option( 'anycomment-social', $option );

		/**
		 * Drop email queue table if exist, will be ignored when not exists
		 */
		global $wpdb;
		$sql = sprintf( "DROP TABLE IF EXISTS `%s`;", $this->getTable() );
		$wpdb->query( $sql );

		return $isVkOptionUpdated;
	}
}