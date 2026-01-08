<?php
namespace Energ\Middleware;

use Energ\Auth\Jwt;
use WP_Error;

class JwtAuth {

    public static function allow($request) {

        $auth = $request->get_header('authorization');

        if (!$auth || !preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            return new WP_Error(
                'missing_token',
                'Authorization token missing',
                ['status' => 401]
            );
        }

        $payload = Jwt::verify($matches[1]);

        if (!$payload) {
            return new WP_Error(
                'invalid_token',
                'Invalid or expired token',
                ['status' => 401]
            );
        }

        // attach user to request
        $request->set_param('auth_user', $payload['sub']);

        return true; // 🔥 THIS IS IMPORTANT
    }
}
