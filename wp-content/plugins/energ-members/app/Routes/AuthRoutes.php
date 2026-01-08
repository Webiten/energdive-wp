<?php
defined('ABSPATH') || exit;

use Energ\API\AuthController;

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

});
