<?php
namespace Energ\Auth;

use WP_Error;

class LogoutAll {

    public function handle($request) {
        global $wpdb;

        $identifier = $request->get_param('auth_identifier');

        if (!$identifier) {
            return new WP_Error(
                'unauthorized',
                'Unauthorized',
                ['status' => 401]
            );
        }

        $wpdb->update(
            $wpdb->prefix . 'energ_refresh_tokens',
            ['revoked' => 1],
            ['identifier' => $identifier],
            ['%d'],
            ['%s']
        );

        return [
            'success' => true,
            'message' => 'Logged out from all devices'
        ];
    }
}
