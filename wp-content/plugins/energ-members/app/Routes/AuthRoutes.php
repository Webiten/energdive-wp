<?php
namespace Energ\Routes;

use Energ\API\AuthController;

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
        'callback' => [AuthController::class, 'me'],
        'permission_callback' => function ($request) {

            // 🔐 SAFE LOAD (NO FATAL)
            if (!class_exists(\Energ\Middleware\JwtAuth::class)) {
                return false;
            }

            return \Energ\Middleware\JwtAuth::allow($request);
        },
    ]);

});