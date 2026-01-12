<?php

namespace Energ\Routes;

use Energ\Auth\AdminRevoke;
use Energ\Middleware\AdminOnly;

add_action('rest_api_init', function () {

    // 🔥 Revoke ALL sessions of a user
    register_rest_route('energ/v1/admin', '/revoke-user', [
        'methods'  => 'POST',
        'callback' => function ($request) {
            return AdminRevoke::revokeAll(
                sanitize_email($request['email'] ?? '')
            );
        },
        'permission_callback' => [AdminOnly::class, 'allow'],
    ]);

    // 🔥 Revoke ONE session
    register_rest_route('energ/v1/admin', '/revoke-session', [
        'methods'  => 'POST',
        'callback' => function ($request) {
            return AdminRevoke::revokeSingle(
                $request['refresh_token'] ?? ''
            );
        },
        'permission_callback' => [AdminOnly::class, 'allow'],
    ]);

});
