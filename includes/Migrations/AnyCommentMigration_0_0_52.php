<?php

namespace AnyComment\Migrations;

class AnyCommentMigration_0_0_52 extends AnyCommentMigration
{
    public $table = 'uploaded_files';
    public $version = '0.0.52';

    /**
     * {@inheritdoc}
     */
    public function is_applied()
    {
        global $wpdb;
        $res = $wpdb->get_results("SHOW TABLES LIKE 'anycomment_uploaded_files';", 'ARRAY_A');

        return !empty($res) && count($res) == 1;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table = 'anycomment_uploaded_files';

        /**
         * Create email queue table
         */
        $sql = "CREATE TABLE `$table` (
			  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `post_ID` bigint(20) UNSIGNED NOT NULL,
			  `user_ID` bigint(20) UNSIGNED DEFAULT NULL,
			  `type` VARCHAR(255) NOT NULL,
			  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `url_thumbnail` VARCHAR(255) DEFAULT NULL,
			  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `created_at` bigint(20) NOT NULL,
			  PRIMARY KEY (`ID`),
			  INDEX post_ID (`post_ID`),
			  INDEX user_ID (`user_ID`),
			  INDEX ip_created_at (`ip`, `created_at`)
			) $charset_collate;";

        return $wpdb->query($sql) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        global $wpdb;
        $sql = "DROP TABLE IF EXISTS `anycomment_uploaded_files`;";
        $wpdb->query($sql);

        return true;
    }
}

// eof;
