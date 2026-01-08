<?php
namespace Energ\API;

use Energ\Auth\RequestOtp;
use Energ\Auth\VerifyOtp;
use Energ\Auth\Jwt;

defined('ABSPATH') || exit;

class AuthController {

    public static function requestOtp($req) {
        return (new RequestOtp)->handle($req);
    }

    public static function verifyOtp($req) {
        return (new VerifyOtp)->handle($req);
    }

    public static function refreshToken($req) {
        return Jwt::refresh($req);
    }
}
