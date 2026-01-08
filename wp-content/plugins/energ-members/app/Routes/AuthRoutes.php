<?php
defined('ABSPATH') || exit;

error_log('ENERG ROUTES LOADED');

add_action('rest_api_init', function () {
    error_log('ENERG REST INIT');

    register_rest_route('energ/v1', '/auth/request-otp', [
        'methods'  => 'POST',
        'callback' => ['Energ\\API\\AuthController', 'requestOtp'],
        'permission_callback' => '__return_true',
    ]);
});
