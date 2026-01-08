<?php
defined('ABSPATH') || exit;

use Energ\API\AuthController;
use Energ\Middleware\JwtAuth;

error_log('ENERG ROUTES FILE LOADED');

add_action('rest_api_init', function () {

    error_log('ENERG REST API INIT');

    register_rest_route('energ/v1', '/auth/request-otp', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'requestOtp'],
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('energ/v1', '/auth/verify-otp', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'verifyOtp'],
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('energ/v1', '/auth/refresh', [
        'methods'  => 'POST',
        'callback' => [AuthController::class, 'refresh'],
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('energ/v1', '/me', [
        'methods'  => 'GET',
        'callback' => function ($request) {

            $user = $request->get_param('auth_user');

            if (!$user) {
                return new \WP_Error(
                    'unauthorized',
                    'Invalid or missing token',
                    ['status' => 401]
                );
            }

            return [
                'success' => true,
                'user' => $user
            ];
        },
        'permission_callback' => [JwtAuth::class, 'allow'],
    ]);
});
