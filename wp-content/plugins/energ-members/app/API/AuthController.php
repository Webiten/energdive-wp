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
        return [
            'success' => true,
            'user' => $request->get_param('auth_user'),
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
