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
// frontend SHORTCODES
// ===============================
add_action('init', function () {
    if (class_exists('Energ\\Frontend\\Shortcodes')) {
        \Energ\Frontend\Shortcodes::register();
        return;
    }

    $file = __DIR__ . '/app/Frontend/Shortcodes.php';
    if (file_exists($file)) {
        require_once $file;
        \Energ\Frontend\Shortcodes::register();
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

    if (!is_singular()) return;

    global $post;
    if (!$post || empty($post->post_content)) return;

    $needs_dashboard = has_shortcode($post->post_content, 'energ_members_dashboard');
    $needs_auth      = has_shortcode($post->post_content, 'energ_members_auth');

    if (!$needs_dashboard && !$needs_auth) return;

    // =========================
    // 🔐 AUTH UI (OLD)
    // =========================
    if ($needs_auth) {
        wp_enqueue_style(
            'energ-members-ui',
            plugin_dir_url(__FILE__) . 'assets/css/energ-ui.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'energ-members-auth',
            plugin_dir_url(__FILE__) . 'assets/js/auth.js',
            [],
            '1.0.0',
            true
        );
    }

    // =========================
    // 📊 REACT DASHBOARD (NEW)
    // =========================
    if ($needs_dashboard) {

        $dist_url = plugin_dir_url(__FILE__) . 'frontend/energ-members-dashboard/dist/';

        wp_enqueue_style(
            'energ-dashboard-style',
            $dist_url . 'assets/index.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'energ-dashboard-app',
            $dist_url . 'assets/index.js',
            [],
            '1.0.0',
            true
        );

        wp_add_inline_script(
            'energ-dashboard-app',
            'window.ENERG = ' . wp_json_encode([
                'api'   => rest_url('energ/v1'),
                'nonce' => wp_create_nonce('wp_rest'),
                'home'  => home_url('/'),
            ]) . ';',
            'before'
        );
    }
});

add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'energ-dashboard-app') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);
