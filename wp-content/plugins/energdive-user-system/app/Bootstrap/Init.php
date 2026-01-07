<?php
namespace Energ\Bootstrap;

defined('ABSPATH') || exit;

class Init {

    public static function boot() {

        // Load Auth handlers
        require_once ENERGDIVE_USER_PLUGIN_PATH . 'app/Auth/RequestOtp.php';
        require_once ENERGDIVE_USER_PLUGIN_PATH . 'app/Auth/VerifyOtp.php';
        require_once ENERGDIVE_USER_PLUGIN_PATH . 'app/Auth/Jwt.php';

        add_action('rest_api_init', function () {

            register_rest_route('energdive/v1', '/auth/request-otp', [
                'methods'  => 'POST',
                'callback' => ['Energ\\Auth\\RequestOtp', 'handle'],
                'permission_callback' => '__return_true',
            ]);

            register_rest_route('energdive/v1', '/auth/verify-otp', [
                'methods'  => 'POST',
                'callback' => ['Energ\\Auth\\VerifyOtp', 'handle'],
                'permission_callback' => '__return_true',
            ]);

        });
    }
}
