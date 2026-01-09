<?php
namespace Energ\Middleware;

use Energ\Auth\Jwt;

class JwtAuth {

    public static function allow($request) {

        $auth = $request->get_header('authorization');

        if (!$auth || !preg_match('/Bearer\s(\S+)/', $auth, $m)) {
            return false;
        }

        $payload = Jwt::verify($m[1]);

        if (!$payload) {
            return false;
        }

        $request->set_param('auth_user', $payload['sub']);
        return true;
    }
}
