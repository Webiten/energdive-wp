<?php

namespace Energ\API;

use Energ\Auth\RequestOtp;
use Energ\Auth\VerifyOtp;
use Energ\Auth\Jwt;

defined('ABSPATH') || exit;

class AuthController
{

    public static function requestOtp($req)
    {
        return (new RequestOtp)->handle($req);
    }

    public static function verifyOtp($req)
    {
        return (new VerifyOtp)->handle($req);
    }

    public static function refreshToken($req)
    {
        return Jwt::refresh($req);
    }

    public static function refresh($request)
    {
        global $wpdb;
        $token = $request->get_json_params()['refresh_token'] ?? '';

        $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}energ_refresh_tokens");
        if (!$row || !password_verify($token, $row->token_hash)) {
            return new \WP_Error('invalid_refresh', 'Invalid refresh token', ['status' => 401]);
        }

        $access = \Energ\Auth\Jwt::generate(['sub' => $row->user_identifier], 15 * 60);
        return ['access_token' => $access, 'expires_in' => 900];
    }
}
