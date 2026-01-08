<?php
namespace Energ\Middleware;

use Energ\Auth\Jwt;
use WP_Error;

class JwtAuth {

    public static function allow($request) {

        $auth = $request->get_header('authorization');

        if (!$auth) {
            return new WP_Error(
                'missing_auth_header',
                'Authorization header missing',
                ['status' => 401]
            );
        }

        if (!preg_match('/Bearer\s+(.+)/i', $auth, $matches)) {
            return new WP_Error(
                'invalid_auth_header',
                'Invalid Authorization header format',
                ['status' => 401]
            );
        }

        $token = trim($matches[1]);

        $payload = Jwt::verify($token);

        if (!$payload || empty($payload['sub'])) {
            return new WP_Error(
                'invalid_token',
                'Invalid or expired token',
                ['status' => 401]
            );
        }

        // 🔥 Attach user to request
        $request->set_param('auth_user', $payload['sub']);

        return true; // 🔥 THIS IS THE KEY
    }
}
