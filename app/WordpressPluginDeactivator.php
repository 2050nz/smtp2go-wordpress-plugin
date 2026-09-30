<?php

namespace SMTP2GO\App;

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.smtp2go.com
 * @since      1.0.1
 *
 * @package    SMTP2GO\WordpressPlugin

 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.1
 * @package    SMTP2GO\WordpressPlugin
 * @subpackage SMTP2GO\WordpressPlugin/includes
 * @author     SMTP2GO <ticket@smtp2go.com>
 */
class WordpressPluginDeactivator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.1
     */
    public static function deactivate()
    {
        wp_clear_scheduled_hook('smtp2go_purge_queued_emails');
    }

    public static function uninstall()
    {
        foreach (
            [
                'smtp2go_api_key',
                'smtp2go_api_region',
                'smtp2go_custom_headers',
                'smtp2go_enable_api_logs',
                'smtp2go_enable_queued_sending',
                'smtp2go_enabled',
                'smtp2go_force_from_address',
                'smtp2go_from_address',
                'smtp2go_from_name'
            ] as $option_name
        ) {
            delete_option($option_name);
        }
        require_once plugin_dir_path(__FILE__) . 'migrations/CreateApiLogsTable.php';
        require_once plugin_dir_path(__FILE__) . 'migrations/CreateQueuedMailTable.php';

        \SMTP2GO\App\Migrations\CreateApiLogsTable::drop();
        \SMTP2GO\App\Migrations\CreateQueuedMailTable::drop();
    }
}
