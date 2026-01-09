<?php

namespace Energ\Routes;

use Energ\API\AuthController;
use Energ\Middleware\JwtAuth;
use WP_Error;

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

            // 🚨 Safety: class must exist
            if (!class_exists(JwtAuth::class)) {
                return new WP_Error(
                    'auth_system_error',
                    'JWT middleware not loaded',
                    ['status' => 500]
                );
            }

            // 🔐 Validate JWT
            $allowed = JwtAuth::allow($request);

            if ($allowed !== true) {
                return new WP_Error(
                    'invalid_token',
                    'Invalid or expired token',
                    ['status' => 401]
                );
            }

            return true;
        },
    ]);

    register_rest_route('energ/v1', '/auth/refresh-token', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'refreshToken'],
        'permission_callback' => '__return_true',
    ]);
});
