<?php

/**
 * Plugin Name: Energ Members (Stable)
 * Description: OTP login + JWT auth system
 * Version: 1.0.0
 * Author: Sankalp
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    error_log('ENERG MEMBERS PLUGIN INIT LOADED');
});


// ===============================
// PSR-4 AUTOLOADER
// ===============================
spl_autoload_register(function ($class) {

    $prefix = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});


// ===============================
// BOOT ROUTES (ONLY THIS)
// ===============================
require_once __DIR__ . '/app/Routes/AuthRoutes.php';


register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('energ_cleanup_cron')) {
        wp_schedule_event(time(), 'hourly', 'energ_cleanup_cron');
    }
});

add_action('energ_cleanup_cron', function () {
    global $wpdb;

    // 🧹 Expired OTPs
    $wpdb->query(
        "DELETE FROM {$wpdb->prefix}energ_otps
         WHERE expires_at < NOW()"
    );

    // 🧹 Expired refresh tokens
    $wpdb->query(
        "DELETE FROM {$wpdb->prefix}energ_refresh_tokens
         WHERE expires_at < NOW()"
    );
});

add_action('energ_cleanup_otp_limits', function () {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->prefix}energ_otp_limits
         WHERE last_attempt < NOW() - INTERVAL 1 DAY"
    );
});

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_script(
        'energ-auth',
        plugin_dir_url(__FILE__) . 'assets/js/auth.js',
        [],
        time(),
        true
    );

    wp_localize_script('energ-auth', 'ENERG', [
        'api' => rest_url('energ/v1'),
        'nonce' => wp_create_nonce('wp_rest')
    ]);

});
add_action('wp_footer', function () {
    echo '<script src="' . plugin_dir_url(__FILE__) . 'assets/js/auth.js"></script>';
});


