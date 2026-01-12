<?php

namespace Energ\Routes;

use Energ\API\AuthController;
use Energ\Middleware\JwtAuth;
use WP_Error;
use Energ\Auth\Logout;
use Energ\Auth\LogoutAll;

defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    // ===============================
    // REQUEST OTP (PUBLIC)
    // ===============================
    register_rest_route('energ/v1', '/auth/request-otp', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'requestOtp'],
        'permission_callback' => '__return_true',
    ]);

    // ===============================
    // VERIFY OTP (PUBLIC)
    // ===============================
    register_rest_route('energ/v1', '/auth/verify-otp', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'verifyOtp'],
        'permission_callback' => '__return_true',
    ]);

    // ===============================
    // CURRENT USER (JWT PROTECTED)
    // ===============================
    register_rest_route('energ/v1', '/me', [
        'methods'  => 'GET',
        'callback' => [\Energ\API\AuthController::class, 'me'],
        'permission_callback' => function ($request) {

            if (!class_exists(\Energ\Middleware\JwtAuth::class)) {
                return false;
            }

            return \Energ\Middleware\JwtAuth::allow($request);
        },
    ]);


    register_rest_route('energ/v1', '/auth/refresh-token', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'refreshToken'],
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('energ/v1', '/auth/logout', [
        'methods'  => 'POST',
        'callback' => [\Energ\API\AuthController::class, 'logout'],
        'permission_callback' => function ($request) {
            return \Energ\Middleware\JwtAuth::allow($request);
        }
    ]);
});

register_rest_route('energ/v1', '/auth/logout', [
    'methods'  => 'POST',
    'callback' => function ($req) {
        return (new Logout)->handle($req);
    },
    'permission_callback' => '__return_true'
]);

register_rest_route('energ/v1', '/auth/logout-all', [
    'methods'  => 'POST',
    'callback' => function ($req) {
        return (new LogoutAll)->handle($req);
    },
    'permission_callback' => function ($req) {
        return \Energ\Middleware\JwtAuth::allow($req);
    }
]);
