<?php
namespace Energ\Auth;

use WP_Error;
use wpdb;

class RefreshToken {

    public function handle($request) {
        global $wpdb;

        $params  = $request->get_json_params();
        $refresh = sanitize_text_field($params['refresh_token'] ?? '');

        if (!$refresh) {
            return new WP_Error('invalid_token', 'Refresh token required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        // Fetch all valid refresh tokens
        $rows = $wpdb->get_results(
            "SELECT * FROM $table WHERE expires_at > UTC_TIMESTAMP()",
            ARRAY_A
        );

        if (!$rows) {
            return new WP_Error('invalid_token', 'Refresh token expired', ['status' => 401]);
        }

        foreach ($rows as $row) {
            if (password_verify($refresh, $row['token_hash'])) {

                // ✅ Issue new access token
                $jwt = Jwt::issue([
                    'sub'   => $row['identifier'],
                    'scope' => 'user'
                ]);

                return [
                    'success'      => true,
                    'access_token' => $jwt['token'],
                    'expires_in'   => $jwt['expires_in']
                ];
            }
        }

        return new WP_Error('invalid_token', 'Invalid refresh token', ['status' => 401]);
    }
}
