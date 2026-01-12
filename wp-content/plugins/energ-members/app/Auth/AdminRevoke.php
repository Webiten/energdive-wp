<?php

namespace Energ\Auth;

use WP_Error;

class AdminRevoke
{
    public static function revokeAll($identifier)
    {
        global $wpdb;

        if (!$identifier) {
            return new WP_Error('missing_identifier', 'User identifier required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        $wpdb->update(
            $table,
            ['revoked' => 1],
            ['identifier' => $identifier]
        );

        return [
            'success' => true,
            'message' => 'All sessions revoked'
        ];
    }

    public static function revokeSingle($refreshToken)
    {
        global $wpdb;

        if (!$refreshToken) {
            return new WP_Error('missing_token', 'Refresh token required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';

        $wpdb->update(
            $table,
            ['revoked' => 1],
            ['token_hash' => hash('sha256', $refreshToken)]
        );

        return [
            'success' => true,
            'message' => 'Session revoked'
        ];
    }
}
