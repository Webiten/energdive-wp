<?php

/**
 * Plugin Name: Energ Members (Stable)
 * Description: OTP login + JWT auth system
 * Version: 1.0.0
 * Author: Sankalp
 */

defined('ABSPATH') || exit;

/**
 * ===============================
 * PSR-4 AUTOLOADER
 * ===============================
 */
spl_autoload_register(function ($class) {
    $prefix   = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strpos($class, $prefix) !== 0) return;

    $relative = substr($class, strlen($prefix));
    $file     = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/app/Routes/IntelligenceRoutes.php';

add_action('rest_api_init', function () {
    \Energ\Routes\IntelligenceRoutes::register();
});



/**
 * ===============================
 * REST ROUTES
 * ===============================
 */
add_action('rest_api_init', function () {
    require_once __DIR__ . '/app/Routes/AuthRoutes.php';
});


/**
 * ===============================
 * SHORTCODES
 * ===============================
 */
add_action('init', function () {
    $file = __DIR__ . '/app/frontend/Shortcodes.php';
    if (file_exists($file)) {
        require_once $file;
        \Energ\Frontend\Shortcodes::register();
    }
});

/**
 * ===============================
 * FRONTEND ASSETS (REACT DASHBOARD)
 * ===============================
 */
add_action('wp_enqueue_scripts', function () {

    global $post;

    // ✅ Shortcode check ONLY (no is_singular trap)
    if (!$post || empty($post->post_content)) return;
    if (!has_shortcode($post->post_content, 'energ_members_dashboard')) return;

    $dist_path = plugin_dir_path(__FILE__) . 'frontend/energ-members-dashbaord/dist/';
    $dist_url  = plugin_dir_url(__FILE__) . 'frontend/energ-members-dashbaord/dist/';

    if (!is_dir($dist_path)) {
        error_log('❌ ENERG: dist folder not found');
        return;
    }

    // 🔥 Load ALL CSS
    foreach (glob($dist_path . 'assets/*.css') as $css) {
        wp_enqueue_style(
            'energ-dashboard-' . md5($css),
            $dist_url . 'assets/' . basename($css),
            [],
            filemtime($css)
        );
    }

    // 🔥 Load ALL JS
    foreach (glob($dist_path . 'assets/*.js') as $js) {
        wp_enqueue_script(
            'energ-dashboard-' . md5($js),
            $dist_url . 'assets/' . basename($js),
            [],
            filemtime($js),
            true
        );
    }

    // 🔥 Global config for React
    wp_add_inline_script(
        'energ-dashboard-' . md5(glob($dist_path . 'assets/*.js')[0]),
        'window.ENERG = ' . wp_json_encode([
            'api'   => rest_url('energ/v1'),
            'home'  => home_url('/'),
        ]) . '; console.log("🔥 ENERG CONFIG LOADED", window.ENERG);',
        'before'
    );
});



/**
 * ===============================
 * FORCE VITE MODULE SCRIPT
 * ===============================
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'energ-dashboard-app') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);
