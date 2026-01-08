<?php
namespace Energ\Middleware;

use Energ\Auth\Jwt;

class JwtAuth {

    public static function allow($request) {
        $auth = $request->get_header('authorization');

        if (!$auth || !preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            return false;
        }

        $payload = Jwt::verify($matches[1]);
        if (!$payload) {
            return false;
        }

        // Attach user to request
        $request->set_param('auth_user', $payload['sub']);
        return true;
    }
}
