<?php

namespace SMTP2GO\App;

use SMTP2GO\App\Migrations\CreateQueuedMailTable;
use SMTP2GOWPPlugin\SMTP2GO\Contracts\BuildsRequest;
require_once dirname(__FILE__) . '/migrations/CreateQueuedMailTable.php';

class QueuedMailManager
{
    public static function store(BuildsRequest $buildsRequest)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        
        CreateQueuedMailTable::run();

        $payload = json_encode(
                $buildsRequest->buildRequestBody(),
                JSON_INVALID_UTF8_SUBSTITUTE
            );
        if ($payload === false) {
            Logger::errorLog('SMTP2GO: Failed to encode payload for queued email.');
            return false;
        }

        //if the payload is over 5MB, send immediately
        $maxBytes = (int) apply_filters('smtp2go_queued_email_max_bytes', 5 * MB_IN_BYTES);
        if (strlen($payload) > $maxBytes) {
            Logger::errorLog('SMTP2GO: Email payload too large to queue (' . strlen($payload) . ' bytes), sending immediately.');
            return false;
        }
        $insert = [
            'payload'    => $payload,
            'created_at' => current_time('mysql', 1)
        ];
        $wpdb->insert(
            $wpdb->prefix . 'smtp2go_queued_emails',
            $insert
        );
        return $wpdb->insert_id;
    }

    public static function getPayload($id)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $table = $wpdb->prefix . 'smtp2go_queued_emails';
        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id),
            ARRAY_A
        );

        return $result ? json_decode($result['payload'], true) : false;
    }

    public static function delete($id)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $table = $wpdb->prefix . 'smtp2go_queued_emails';
        $wpdb->query(
            $wpdb->prepare("DELETE FROM $table WHERE id = %d", $id)
        );
    }

    /**
     * Delete queued emails older than the given number of days.
     * created_at is stored in GMT.
     *
     * @param int $days
     * @return int|false number of rows deleted
     */
    public static function purgeOlderThan($days)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $table = $wpdb->prefix . 'smtp2go_queued_emails';
        $cutoff = gmdate('Y-m-d H:i:s', time() - (int) $days * DAY_IN_SECONDS);

        return $wpdb->query(
            $wpdb->prepare("DELETE FROM $table WHERE created_at < %s", $cutoff)
        );
    }
}
