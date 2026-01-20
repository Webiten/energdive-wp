<?php

/**
 * Plugin Name: Energ Members (Stable)
 * Description: OTP login + JWT auth system
 * Version: 1.0.0
 * Author: Sankalp
 */

defined('ABSPATH') || exit;
// die('ENERG MEMBERS PLUGIN LOADED');


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
add_action('rest_api_init', function () {
    require_once __DIR__ . '/app/Routes/AuthRoutes.php';
});

// ===============================
// FRONTEND SHORTCODES
// ===============================
add_action('init', function () {
    // Shortcodes are lightweight and safe to register on init.
    if (class_exists('Energ\\Frontend\\Shortcodes')) {
        \Energ\Frontend\Shortcodes::register();
    } else {
        // File will be present in this plugin; keep require as a safe fallback.
        $file = __DIR__ . '/app/Frontend/Shortcodes.php';
        if (file_exists($file)) {
            require_once $file;
            \Energ\Frontend\Shortcodes::register();
        }
    }
});



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
    // Enqueue only on pages where shortcodes are present.
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || empty($post->post_content)) {
        return;
    }

    $needs = has_shortcode($post->post_content, 'energ_members_auth')
        || has_shortcode($post->post_content, 'energ_members_dashboard');

    if (!$needs) {
        return;
    }

    wp_enqueue_style(
        'energ-members-ui',
        plugin_dir_url(__FILE__) . 'assets/css/energ-ui.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'energ-members-ui',
        plugin_dir_url(__FILE__) . 'assets/js/auth.js',
        [],
        '1.0.0',
        true
    );

    wp_localize_script('energ-members-ui', 'ENERG', [
        'api'   => rest_url('energ/v1'),
        'nonce' => wp_create_nonce('wp_rest'),
        'home'  => home_url('/'),
    ]);
});



wp_enqueue_style(
  'energ-complete',
  plugin_dir_url(__FILE__) . '../assets/css/energ-complete.css'
);

wp_enqueue_script(
  'energ-complete',
  plugin_dir_url(__FILE__) . '../assets/js/energ-complete.js',
  ['jquery'],
  null,
  true
);

wp_localize_script('energ-complete','ENERG',[
  'ajax'=>admin_url('admin-ajax.php')
]);
