<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('energ/v1', '/auth/request-otp', [
        'methods'  => 'POST',
        'callback' => ['Energ\\API\\AuthController', 'requestOtp'],
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('energ/v1', '/auth/verify-otp', [
        'methods'  => 'POST',
        'callback' => ['Energ\\API\\AuthController', 'verifyOtp'],
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('energ/v1', '/auth/refresh', [
        'methods'  => 'POST',
        'callback' => ['Energ\\API\\AuthController', 'refreshToken'],
        'permission_callback' => '__return_true',
    ]);
});
