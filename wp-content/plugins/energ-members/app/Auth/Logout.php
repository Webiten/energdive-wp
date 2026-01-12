<?php
namespace Energ\Auth;

use WP_Error;

class Logout {

    public function handle($request) {
        global $wpdb;

        $params = $request->get_json_params();
        $refresh = $params['refresh_token'] ?? '';

        if (!$refresh) {
            return new WP_Error(
                'missing_refresh_token',
                'Refresh token required',
                ['status' => 400]
            );
        }

        $hash = hash('sha256', $refresh);

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        $updated = $wpdb->update(
            $table,
            ['revoked' => 1],
            ['token_hash' => $hash],
            ['%d'],
            ['%s']
        );

        if (!$updated) {
            return new WP_Error(
                'invalid_token',
                'Invalid or expired token',
                ['status' => 401]
            );
        }

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
}
