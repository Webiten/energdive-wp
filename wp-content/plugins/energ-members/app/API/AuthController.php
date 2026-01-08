<?php
namespace Energ\API;

use WP_REST_Request;
use WP_Error;

class AuthController {

    public static function requestOtp(WP_REST_Request $request) {
        return (new \Energ\Auth\RequestOtp())->handle($request);
    }

    public static function verifyOtp(WP_REST_Request $request) {
        return (new \Energ\Auth\VerifyOtp())->handle($request);
    }

    // 🔥 THIS WAS THE MISSING / BROKEN PART
    public static function me(WP_REST_Request $request) {

        $user = $request->get_param('auth_user');

        if (!$user) {
            return new WP_Error(
                'unauthorized',
                'Invalid or missing token',
                ['status' => 401]
            );
        }

        return [
            'success' => true,
            'user' => [
                'email' => $user,
            ],
        ];
    }
}
