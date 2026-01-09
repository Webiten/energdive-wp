<?php

namespace Energ\Auth;

use WP_Error;

class RefreshToken
{

    public function handle($request)
    {
        global $wpdb;

        $params = $request->get_json_params();
        $token  = $params['refresh_token'] ?? '';

        if (!$token) {
            return new WP_Error('missing_token', 'Refresh token required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        // 🔍 Find token
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT identifier FROM {$wpdb->prefix}energ_refresh_tokens
                WHERE token_hash = %s AND expires_at > NOW()",
                hash('sha256', $refreshToken)
            )
        );

        if (!$row) {
            return new \WP_Error('invalid_token', 'Invalid or expired refresh token', ['status' => 401]);
        }

        $email = $row->identifier;


        if (!$row || strtotime($row->expires_at) < time()) {
            return new WP_Error('invalid_token', 'Invalid or expired refresh token', ['status' => 401]);
        }

        // 🔥 ROTATION: delete old token
        $wpdb->delete($table, ['id' => $row->id]);

        // 🔐 Issue NEW JWT
        $jwt = Jwt::issue([
            'sub'   => $row->identifier,
            'scope' => 'user'
        ]);

        // 🔁 Issue NEW refresh token
        $newRefresh = bin2hex(random_bytes(32));

        $wpdb->insert($table, [
            'identifier' => $row->identifier,
            ['token_hash' => hash('sha256', $newRefresh),]
        ]);

        return [
            'success'        => true,
            'access_token'  => $jwt['token'],
            'refresh_token' => $newRefresh,
            'expires_in'    => $jwt['expires_in']
        ];
    }
}
