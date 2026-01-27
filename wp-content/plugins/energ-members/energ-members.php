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

    // 🔥 FORCE LOAD ON DASHBOARD PAGE
    if (!is_page() || !str_contains($_SERVER['REQUEST_URI'], '/dashboard')) {
        return;
    }

    $dist_path = plugin_dir_path(__FILE__) . 'frontend/energ-members-dashbaord/dist/';
    $dist_url  = plugin_dir_url(__FILE__) . 'frontend/energ-members-dashbaord/dist/';

    // 🔥 HARD FAIL LOG
    error_log('🔥 ENERG DIST PATH = ' . $dist_path);

    if (!file_exists($dist_path . 'index.html')) {
        error_log('❌ ENERG: index.html missing in dist');
        return;
    }

    // 🔥 DO NOT USE glob (rsync breaks timestamps)
    $css = $dist_url . 'assets/index.css';
    $js  = $dist_url . 'assets/index.js';

    // 🔥 AUTO-DETECT REAL FILES
    foreach (scandir($dist_path . 'assets/') as $file) {
        if (str_ends_with($file, '.css')) {
            $css = $dist_url . 'assets/' . $file;
        }
        if (str_ends_with($file, '.js')) {
            $js = $dist_url . 'assets/' . $file;
        }
    }

    error_log('🔥 ENERG CSS = ' . $css);
    error_log('🔥 ENERG JS = ' . $js);

    wp_enqueue_style('energ-dashboard-style', $css, [], null);
    wp_enqueue_script('energ-dashboard-app', $js, [], null, true);

    wp_add_inline_script(
        'energ-dashboard-app',
        'window.ENERG=' . wp_json_encode([
            'api' => rest_url('energ/v1'),
            'home' => home_url('/')
        ]) . ';console.log("🔥 ENERG LOADED", window.ENERG);',
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
