<?php

namespace AnyComment\Migrations;

/**
 * Class AnyCommentMigration_0_0_59 is used create email queue table if it does not exist and then
 * prefexes existing plugin table with WPDB WordPress's prefix.
 */
class AnyCommentMigration_0_0_59 extends AnyCommentMigration {
	public $table = 'uploaded_files';
	public $version = '0.0.59';

	/**
	 * @var array List of tables to be renamed to a prefixed version.
	 */
	private $_tables_to_rename = [
		'anycomment_email_queue',
		'anycomment_likes',
		'anycomment_uploaded_files',
	];

	/**
	 * {@inheritdoc}
	 */
	public function is_applied() {
		global $wpdb;

		$renamed_count    = 0;
		$tables_to_rename = $this->_tables_to_rename;

		foreach ( $tables_to_rename as $old_name ) {
			$new_name = $wpdb->prefix . $old_name;
			$res      = $wpdb->get_results( "SHOW TABLES LIKE '$new_name';", 'ARRAY_A' );

			if ( ! empty( $res ) && count( $res ) == 1 ) {
				$renamed_count ++;
			}
		}

		return count( $tables_to_rename ) === $renamed_count;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table           = 'anycomment_email_queue';

		$create_email_queue_table = "CREATE TABLE IF NOT EXISTS `$table` (
									  `ID` bigint(20) UNSIGNED NOT NULL,
									  `post_ID` bigint(20) UNSIGNED NOT NULL,
									  `comment_ID` bigint(20) UNSIGNED NOT NULL,
									  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
									  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
									  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
									  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
									  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
									  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
									  `is_sent` tinyint(1) DEFAULT '0'
									) $charset_collate;";

		// Do not care much about result, as table is create if does not exist only
		$wpdb->query( $create_email_queue_table );

		$tables_to_rename     = $this->_tables_to_rename;
		$tables_renamed_count = 0;

		foreach ( $tables_to_rename as $old_name ) {
			$new_name   = $wpdb->prefix . $old_name;
			$is_renamed = $wpdb->query( "RENAME TABLE `$old_name` TO `$new_name`;" ) !== false;

			if ( $is_renamed ) {
				$tables_renamed_count ++;
			}

			$wpdb->query( "DROP TABLE IF EXISTS `$old_name`" );
		}


		return count( $tables_to_rename ) === $tables_renamed_count;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$tables_to_rename     = $this->_tables_to_rename;
		$tables_renamed_count = 0;


		foreach ( $tables_to_rename as $old_name ) {
			$new_name   = $wpdb->prefix . $old_name;
			$is_renamed = $wpdb->query( "RENAME TABLE `$new_name` TO `$old_name`;" ) !== false;

			if ( $is_renamed ) {
				$wpdb->query( "DROP TABLE IF EXISTS `$new_name`" );

				$tables_renamed_count ++;
			}
		}

		return count( $tables_to_rename ) === $tables_renamed_count;
	}
}

// eof;
