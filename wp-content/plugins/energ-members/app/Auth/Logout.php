<?php

namespace Energ\Auth;

use WP_Error;

class Logout
{
    public function handle($request)
    {
        global $wpdb;

        $authUser = $request->get_param('auth_user');

        if (!$authUser) {
            return new WP_Error(
                'unauthorized',
                'User not authenticated',
                ['status' => 401]
            );
        }

        // 🔥 Delete ALL refresh tokens for this user
        $wpdb->delete(
            $wpdb->prefix . 'energ_refresh_tokens',
            ['identifier' => $authUser]
        );

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
}
