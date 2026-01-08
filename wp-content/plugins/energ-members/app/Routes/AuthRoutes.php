<?php
namespace Energ\Routes;

use Energ\API\AuthController;
use Energ\Middleware\JwtAuth;

add_action('rest_api_init', function () {

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

    register_rest_route('energ/v1', '/me', [
        'methods'  => 'GET',
        'callback' => [AuthController::class, 'me'],
        // 🔥 THIS MUST MATCH JwtAuth::allow
        'permission_callback' => [JwtAuth::class, 'allow'],
    ]);

});
