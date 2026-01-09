<?php

namespace Energ\Auth;

use WP_Error;

class OtpRateLimiter
{
    public static function check($identifier)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'energ_otp_limits';
        $ip    = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $now   = current_time('mysql');

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} 
                 WHERE identifier = %s AND ip_address = %s",
                $identifier,
                $ip
            )
        );

        // 🚫 BLOCKED
        if ($row && $row->blocked_until && strtotime($row->blocked_until) > time()) {
            return new WP_Error(
                'otp_rate_limited',
                'Too many OTP requests. Try again later.',
                ['status' => 429]
            );
        }

        // 🆕 FIRST ATTEMPT
        if (!$row) {
            $wpdb->insert($table, [
                'identifier'    => $identifier,
                'ip_address'    => $ip,
                'attempts'      => 1,
                'last_attempt'  => $now,
            ]);

            return true;
        }

        // ⏱ RESET WINDOW (10 min)
        if (strtotime($row->last_attempt) < time() - (10 * MINUTE_IN_SECONDS)) {
            $wpdb->update($table, [
                'attempts'     => 1,
                'last_attempt' => $now,
                'blocked_until'=> null
            ], ['id' => $row->id]);

            return true;
        }

        // ❌ TOO MANY ATTEMPTS
        if ($row->attempts >= 3) {
            $wpdb->update($table, [
                'blocked_until' => gmdate(
                    'Y-m-d H:i:s',
                    time() + (30 * MINUTE_IN_SECONDS)
                )
            ], ['id' => $row->id]);

            return new WP_Error(
                'otp_rate_limited',
                'Too many OTP requests. Try after 30 minutes.',
                ['status' => 429]
            );
        }

        // ➕ INCREMENT ATTEMPT
        $wpdb->update($table, [
            'attempts'     => $row->attempts + 1,
            'last_attempt' => $now,
        ], ['id' => $row->id]);

        return true;
    }
}
