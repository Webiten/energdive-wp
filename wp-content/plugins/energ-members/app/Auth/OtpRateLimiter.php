<?php

namespace Energ\Auth;

use WP_Error;

class OtpRateLimiter
{
    const MAX_ATTEMPTS = 3;
    const WINDOW      = 600;   // 10 minutes
    const BLOCK_TIME  = 1800;  // 30 minutes

    public static function check($identifier)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'energ_otp_limits';
        $ip    = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $now   = time();

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table}
                 WHERE identifier = %s AND ip_address = %s",
                $identifier,
                $ip
            )
        );

        // 🚫 Already blocked
        if ($row && $row->blocked_until && strtotime($row->blocked_until) > $now) {
            return new WP_Error(
                'otp_rate_limited',
                'Too many OTP requests. Try again later.',
                ['status' => 429]
            );
        }

        // 🆕 First request
        if (!$row) {
            $wpdb->insert($table, [
                'identifier'   => $identifier,
                'ip_address'   => $ip,
                'attempts'     => 1,
                'last_attempt' => current_time('mysql'),
            ]);
            return true;
        }

        // ⏱ Reset window
        if (strtotime($row->last_attempt) < ($now - self::WINDOW)) {
            $wpdb->update($table, [
                'attempts'      => 1,
                'last_attempt'  => current_time('mysql'),
                'blocked_until' => null
            ], ['id' => $row->id]);
            return true;
        }

        // ❌ Too many attempts
        if ($row->attempts >= self::MAX_ATTEMPTS) {
            $wpdb->update($table, [
                'blocked_until' => gmdate(
                    'Y-m-d H:i:s',
                    $now + self::BLOCK_TIME
                )
            ], ['id' => $row->id]);

            return new WP_Error(
                'otp_rate_limited',
                'Too many OTP requests. Try again after 30 minutes.',
                ['status' => 429]
            );
        }

        // ➕ Increment attempt
        $wpdb->update($table, [
            'attempts'     => $row->attempts + 1,
            'last_attempt' => current_time('mysql'),
        ], ['id' => $row->id]);

        return true;
    }
}
