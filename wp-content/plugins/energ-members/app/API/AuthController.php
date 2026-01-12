<?php

namespace Energ\API;

use Energ\Auth\RequestOtp;
use Energ\Auth\VerifyOtp;
use Energ\Auth\RefreshToken;

class AuthController
{

    public static function requestOtp($request)
    {
        return (new RequestOtp)->handle($request);
    }

    public static function verifyOtp($request)
    {
        return (new VerifyOtp)->handle($request);
    }

    public static function me($request)
    {
        global $wpdb;

        $email = $request->get_param('auth_identifier');

        if (!$email) {
            return new \WP_Error(
                'unauthorized',
                'Invalid or missing token',
                ['status' => 401]
            );
        }

        $table = $wpdb->prefix . 'energ_members';

        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, email, phone, signup_mode, created_at
             FROM {$table}
             WHERE email = %s
             LIMIT 1",
                $email
            ),
            ARRAY_A
        );

        if (!$user) {
            return new \WP_Error(
                'user_not_found',
                'User not found',
                ['status' => 404]
            );
        }

        return [
            'success' => true,
            'user'    => $user
        ];
    }

    
    public static function refreshToken($request)
    {
        return (new RefreshToken)->handle($request);
    }

    public static function logout($request)
    {
        return (new \Energ\Auth\Logout)->handle($request);
    }
}
