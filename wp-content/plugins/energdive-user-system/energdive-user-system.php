// TEST COMMIT - REMOVE LATER
<?php
/**
 * Plugin Name: Energdive User System
 * Description: High-scale custom user authentication system (OTP + Password + Session abstraction).
 * Version: 1.0.0
 * Author: Energdive
 */

defined('ABSPATH') || exit;

define('ENERGDIVE_USER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ENERGDIVE_USER_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once ENERGDIVE_USER_PLUGIN_PATH . 'app/Bootstrap/Activator.php';
require_once ENERGDIVE_USER_PLUGIN_PATH . 'app/Bootstrap/Init.php';

register_activation_hook(__FILE__, ['Energ\Bootstrap\Activator', 'activate']);

add_action('plugins_loaded', function () {
    Energ\Bootstrap\Init::boot();
});
