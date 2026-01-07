<?php
namespace Energ\Bootstrap;

defined('ABSPATH') || exit;

class Init {

    public static function boot() {
        require_once ENERGDIVE_USER_PLUGIN_PATH . 'app/API/AuthController.php';
        add_action('rest_api_init', ['Energ\\API\\AuthController', 'register_routes']);
    }
}
