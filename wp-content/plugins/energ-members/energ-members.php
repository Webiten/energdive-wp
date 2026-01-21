<?php
die('🔥 ENERG PLUGIN LOADED');
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
 * FRONTEND ASSETS (FORCE LOAD)
 * ===============================
 */
add_action('wp_enqueue_scripts', function () {

    $dist_path = plugin_dir_path(__FILE__) . 'frontend/energ-members-dashbaord/dist/';
    $dist_url  = plugin_dir_url(__FILE__) . 'frontend/energ-members-dashbaord/dist/';

    if (!is_dir($dist_path)) return;

    $jsFiles  = glob($dist_path . 'assets/index-*.js');
    $cssFiles = glob($dist_path . 'assets/index-*.css');

    if (empty($jsFiles)) return;

    if (!empty($cssFiles)) {
        wp_enqueue_style(
            'energ-dashboard-style',
            $dist_url . 'assets/' . basename($cssFiles[0]),
            [],
            time()
        );
    }

    wp_enqueue_script(
        'energ-dashboard-app',
        $dist_url . 'assets/' . basename($jsFiles[0]),
        [],
        time(),
        true
    );

    wp_add_inline_script(
        'energ-dashboard-app',
        'window.ENERG = ' . wp_json_encode([
            'api'   => rest_url('energ/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'home'  => home_url('/'),
        ]) . ';
        console.log("🔥 ENERG INLINE OK", window.ENERG);',
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
