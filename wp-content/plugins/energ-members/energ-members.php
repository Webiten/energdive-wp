<?php
/**
 * Plugin Name: Energ Members (Stable)
 * Description: OTP login + JWT auth system
 * Version: 1.0.0
 * Author: Sankalp
 */

defined('ABSPATH') || exit;

/* ===============================
   PSR-4 AUTOLOADER
================================ */
spl_autoload_register(function ($class) {
    $prefix   = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/* ===============================
   API ROUTES
================================ */
add_action('rest_api_init', function () {
    require_once __DIR__ . '/app/Routes/AuthRoutes.php';
});

/* ===============================
   SHORTCODES
================================ */
add_action('init', function () {
    // EnergClub (PHP UI)
    if (class_exists('Energ\\Frontend\\EnergClubShortcodes')) {
        \Energ\Frontend\EnergClubShortcodes::register();
    }

    // Legacy shortcodes (optional)
    if (class_exists('Energ\\Frontend\\Shortcodes')) {
        \Energ\Frontend\Shortcodes::register();
    }
});

/* ===============================
   CRON CLEANUP
================================ */
register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('energ_cleanup_cron')) {
        wp_schedule_event(time(), 'hourly', 'energ_cleanup_cron');
    }
});

add_action('energ_cleanup_cron', function () {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->prefix}energ_otps WHERE expires_at < NOW()");
    $wpdb->query("DELETE FROM {$wpdb->prefix}energ_refresh_tokens WHERE expires_at < NOW()");
});

/* ===============================
   AJAX: Area of Industry map
================================ */
add_action('wp_ajax_energclub_area_map', function () {
    $community     = sanitize_text_field($_POST['community'] ?? '');
    $sub_community = sanitize_text_field($_POST['sub_community'] ?? '');

    if (!class_exists('Energ\\Frontend\\EnergClubData')) {
        wp_send_json_error(['message' => 'EnergClubData missing']);
    }

    $area = \Energ\Frontend\EnergClubData::areaOfIndustry($community, $sub_community);
    wp_send_json_success(['area' => $area]);
});

add_action('wp_ajax_nopriv_energclub_area_map', function () {
    $community     = sanitize_text_field($_POST['community'] ?? '');
    $sub_community = sanitize_text_field($_POST['sub_community'] ?? '');

    if (!class_exists('Energ\\Frontend\\EnergClubData')) {
        wp_send_json_error(['message' => 'EnergClubData missing']);
    }

    $area = \Energ\Frontend\EnergClubData::areaOfIndustry($community, $sub_community);
    wp_send_json_success(['area' => $area]);
});

/* ===============================
   FRONTEND ASSETS
================================ */
add_action('wp_enqueue_scripts', function () {

    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || empty($post->post_content)) {
        return;
    }

    $needs_energclub = (
        has_shortcode($post->post_content, 'energclub_login') ||
        has_shortcode($post->post_content, 'energclub_register') ||
        has_shortcode($post->post_content, 'energclub_dashboard')
    );

    if (!$needs_energclub) {
        return;
    }

    wp_enqueue_style(
        'energclub-css',
        plugin_dir_url(__FILE__) . 'assets/css/energclub.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'energclub-js',
        plugin_dir_url(__FILE__) . 'assets/js/energclub.js',
        [],
        '1.0.0',
        true
    );
});
