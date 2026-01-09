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

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        $deleted = $wpdb->delete(
            $table,
            ['token_hash' => hash('sha256', $refresh)]
        );

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
}
