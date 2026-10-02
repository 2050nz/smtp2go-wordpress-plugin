<?php

namespace SMTP2GO\App\Migrations;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

final class CreateQueuedMailTable
{
    public static function run()
    {
        global $wpdb;       

        $table = $wpdb->prefix . 'smtp2go_queued_emails';

        if (!empty($wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $table)))) {
            return;
        }

        $charsetCollate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,                       
            `payload` LONGTEXT NULL,
            `created_at` TIMESTAMP NULL
        ) $charsetCollate;";

        \dbDelta($sql);
    }

    public static function drop()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'smtp2go_queued_emails';

        if (empty($wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $table)))) {
            return;
        }

        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}
