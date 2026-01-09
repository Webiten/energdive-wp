<?php

namespace Energ\Auth;

use WP_Error;

class RefreshToken
{
    public function handle($request)
{
    global $wpdb;

    $params = $request->get_json_params();
    $refreshToken = $params['refresh_token'] ?? '';

    if (!$refreshToken) {
        return new \WP_Error(
            'missing_token',
            'Refresh token required',
            ['status' => 400]
        );
    }

    $table = $wpdb->prefix . 'energ_refresh_tokens';

    // 🔍 Fetch FULL row
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

    // 🔥 Rotate (delete old)
    $wpdb->delete($table, ['id' => $row->id]);

    // 🔐 Issue NEW JWT (FIXED sub)
    $jwt = Jwt::issue([
        'sub'   => $row->identifier,
        'scope' => 'user'
    ]);

    // 🔁 New refresh token
    $newRefresh = bin2hex(random_bytes(32));

    $wpdb->insert($table, [
        'identifier' => $row->identifier,
        'token_hash' => hash('sha256', $newRefresh),
        'expires_at' => gmdate('Y-m-d H:i:s', time() + (30 * DAY_IN_SECONDS)),
    ]);

    return [
        'success'        => true,
        'access_token'  => $jwt['token'],
        'refresh_token' => $newRefresh,
        'expires_in'    => $jwt['expires_in'],
    ];
}
}
