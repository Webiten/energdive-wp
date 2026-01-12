<?php
namespace Energ\Security;

use WP_Error;

class OtpRateLimiter {

    public static function check($identifier, $ip) {
        global $wpdb;

        $table = $wpdb->prefix . 'energ_otp_limits';
        $now   = current_time('mysql');

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table}
                 WHERE identifier = %s AND ip_address = %s
                 LIMIT 1",
                $identifier,
                $ip
            )
        );

        // 🚫 Blocked
        if ($row && $row->blocked_until && strtotime($row->blocked_until) > time()) {
            return new WP_Error(
                'otp_blocked',
                'Too many OTP requests. Try later.',
                ['status' => 429]
            );
        }

        // 🆕 First attempt
        if (!$row) {
            $wpdb->insert($table, [
                'identifier'   => $identifier,
                'ip_address'   => $ip,
                'attempts'     => 1,
                'last_attempt' => $now,
            ]);
            return true;
        }

        // 🔁 Within window
        if (strtotime($row->last_attempt) > time() - (10 * 60)) {

            if ($row->attempts >= 5) {
                $wpdb->update(
                    $table,
                    [
                        'blocked_until' => gmdate(
                            'Y-m-d H:i:s',
                            time() + (30 * 60)
                        )
                    ],
                    ['id' => $row->id]
                );

                return new WP_Error(
                    'otp_blocked',
                    'Too many OTP attempts. Try again later.',
                    ['status' => 429]
                );
            }

            $wpdb->update(
                $table,
                [
                    'attempts'     => $row->attempts + 1,
                    'last_attempt' => $now
                ],
                ['id' => $row->id]
            );

            return true;
        }

        // ⏱ Reset window
        $wpdb->update(
            $table,
            [
                'attempts'     => 1,
                'last_attempt' => $now,
                'blocked_until'=> null
            ],
            ['id' => $row->id]
        );

        return true;
    }
}
