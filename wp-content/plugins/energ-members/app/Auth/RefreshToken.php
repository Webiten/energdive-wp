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

        // Also allow refresh token via HttpOnly cookie
        if (!$refreshToken && isset($_COOKIE['energ_refresh_token'])) {
            $refreshToken = (string) $_COOKIE['energ_refresh_token'];
        }

        if (!$refreshToken) {
            return new WP_Error('missing_token', 'Refresh token required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';
        $hash = hash('sha256', $refreshToken);

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, identifier, expires_at, revoked
                 FROM {$table}
                 WHERE token_hash = %s
                 LIMIT 1",
                $hash
            )
        );

        if (!$row || empty($row->identifier)) {
            return new WP_Error('invalid_token', 'Invalid refresh token', ['status' => 401]);
        }

        if ((int)($row->revoked ?? 0) === 1) {
            return new WP_Error('revoked_token', 'Session revoked. Please log in again.', ['status' => 401]);
        }

        if (!empty($row->expires_at) && strtotime($row->expires_at) < time()) {
            return new WP_Error('expired_token', 'Expired refresh token', ['status' => 401]);
        }

        // ✅ Rotate token IN-PLACE (keep same row id = SID)
        $newRefresh = bin2hex(random_bytes(32));
        $updated = $wpdb->update(
            $table,
            [
                'token_hash'  => hash('sha256', $newRefresh),
                'expires_at'  => gmdate('Y-m-d H:i:s', time() + (30 * DAY_IN_SECONDS)),
                'revoked'     => 0,
                'revoked_at'  => null,
            ],
            ['id' => $row->id],
            ['%s', '%s', '%d', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_Error('server_error', 'Failed to rotate refresh token', ['status' => 500]);
        }

        // ✅ Issue NEW JWT with SAME SID
        $jwt = Jwt::issue(
            [
                'sub'   => $row->identifier,
                'scope' => 'user',
                'sid'   => (int)$row->id,
            ],
            15 * 60
        );

        // Set HttpOnly cookie (optional)
        $secure = is_ssl();
        $cookieArgs = [
            'expires'  => time() + (30 * DAY_IN_SECONDS),
            'path'     => '/',
            'domain'   => defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        if (PHP_VERSION_ID >= 70300) {
            setcookie('energ_refresh_token', $newRefresh, $cookieArgs);
        } else {
            setcookie('energ_refresh_token', $newRefresh, $cookieArgs['expires'], $cookieArgs['path']);
        }

        return [
            'success'       => true,
            'access_token'  => $jwt['token'],
            'refresh_token' => $newRefresh, // keep returning for mobile/API clients
            'expires_in'    => $jwt['expires_in'],
            'sid'           => (int)$row->id,
        ];
    }
}
