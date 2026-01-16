<?php

namespace Energ\Auth;

use WP_Error;

class RefreshToken
{
    public function handle($request)
    {
        global $wpdb;

        $params = method_exists($request, 'get_json_params') ? (array) $request->get_json_params() : [];
        $refreshToken = $params['refresh_token'] ?? '';

        // ALSO allow refresh token via HttpOnly cookie (recommended)
        if (!$refreshToken && isset($_COOKIE['energ_refresh_token'])) {
            $refreshToken = (string) $_COOKIE['energ_refresh_token'];
        }

        if (!$refreshToken) {
            return new \WP_Error(
                'missing_token',
                'Refresh token required',
                ['status' => 400]
            );
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, identifier, expires_at
                 FROM {$table}
                 WHERE token_hash = %s
                 AND expires_at > NOW()",
                hash('sha256', $refreshToken)
            )
        );

        if (!$row || empty($row->identifier)) {
            return new \WP_Error(
                'invalid_token',
                'Invalid or expired refresh token',
                ['status' => 401]
            );
        }

        // Rotate (delete old)
        $wpdb->delete($table, ['id' => $row->id]);

        // Issue NEW JWT
        $jwt = Jwt::issue([
            'sub'   => $row->identifier,
            'scope' => 'user'
        ]);

        // New refresh token
        $newRefresh = bin2hex(random_bytes(32));

        $wpdb->insert($table, [
            'identifier' => $row->identifier,
            'token_hash' => hash('sha256', $newRefresh),
            'expires_at' => gmdate('Y-m-d H:i:s', time() + (30 * DAY_IN_SECONDS)),
        ]);

        // ✅ Set HttpOnly cookie so frontend doesn't need to manage refresh token
        // Adjust COOKIE_DOMAIN/secure based on your environment
        $secure = is_ssl();
        $cookieArgs = [
            'expires'  => time() + (30 * DAY_IN_SECONDS),
            'path'     => '/',
            'domain'   => defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        // PHP < 7.3 doesn't support array options; WordPress servers are usually modern,
        // but keep fallback for safety.
        if (PHP_VERSION_ID >= 70300) {
            setcookie('energ_refresh_token', $newRefresh, $cookieArgs);
        } else {
            setcookie('energ_refresh_token', $newRefresh, $cookieArgs['expires'], $cookieArgs['path']);
        }

        return [
            'success'        => true,
            'access_token'   => $jwt['token'],
            'refresh_token'  => $newRefresh, // keep returning for mobile/API clients
            'expires_in'     => $jwt['expires_in'],
        ];
    }
}
