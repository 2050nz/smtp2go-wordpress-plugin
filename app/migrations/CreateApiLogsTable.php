<?php

namespace SMTP2GO\App\Migrations;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


final class CreateApiLogsTable
{
    /**
     * Tables already checked this request, keyed by table name so each
     * site on multisite (different $wpdb->prefix) is still checked.
     *
     * @var array<string, bool>
     */
    private static $checked = [];

    public static function run()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'smtp2go_api_logs';

        if (isset(self::$checked[$table])) {
            return;
        }
        self::$checked[$table] = true;

        if (!empty($wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $table)))) {
            return;
        }

        $charsetCollate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `site_id` INT UNSIGNED NULL,
            `to` VARCHAR(255),
            `from` VARCHAR(255),
            `subject` VARCHAR(255) NULL,
            `response` LONGTEXT NULL,
            `created_at` TIMESTAMP NULL
        ) $charsetCollate;";

        \dbDelta($sql);
    }

    public static function drop()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'smtp2go_api_logs';
        unset(self::$checked[$table]);

        if (empty($wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $table)))) {
            return;
        }

        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}
