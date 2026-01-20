<?php

namespace Energ\Middleware;

use Energ\Auth\Jwt;
use WP_Error;

class JwtAuth
{
    public static function allow($request)
    {
        global $wpdb;

        $auth = $request->get_header('authorization');

        if (!$auth || !preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            return new WP_Error('missing_token', 'Authorization token missing', ['status' => 401]);
        }

        $token = $matches[1];
        $payload = Jwt::verify($token);

        if (!$payload || empty($payload['sub'])) {
            return new WP_Error('invalid_token', 'Invalid or expired token', ['status' => 401]);
        }

        $identifier = (string) $payload['sub'];
        $sid = isset($payload['sid']) ? (int) $payload['sid'] : 0;

        /**
         * ✅ Enforce session revocation using SID (refresh token row id)
         */
        if ($sid > 0) {
            $table = $wpdb->prefix . 'energ_refresh_tokens';

            $row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT revoked, expires_at
                     FROM {$table}
                     WHERE id = %d AND identifier = %s
                     LIMIT 1",
                    $sid,
                    $identifier
                ),
                ARRAY_A
            );

            if (!$row) {
                return new WP_Error('session_missing', 'Session not found. Please log in again.', ['status' => 401]);
            }

            if ((int)($row['revoked'] ?? 0) === 1) {
                return new WP_Error('revoked_token', 'Session revoked. Please log in again.', ['status' => 401]);
            }

            // If session itself expired, force login
            if (!empty($row['expires_at']) && strtotime($row['expires_at']) < time()) {
                return new WP_Error('session_expired', 'Session expired. Please log in again.', ['status' => 401]);
            }
        }

        // Standardize params for controllers
        $request->set_param('auth_identifier', $identifier);
        $request->set_param('auth_user', $identifier);

        // Helpful for logout (sid-based revoke)
        $request->set_param('auth_sid', $sid);
        $request->set_param('auth_token', $token);

        return true;
    }
}
