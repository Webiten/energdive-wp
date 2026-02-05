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

add_action('rest_api_init', function () {
    \Energ\Routes\UserRoutes::register();
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

    if (!is_page() || !str_contains($_SERVER['REQUEST_URI'], '/dashboard')) {
        return;
    }

    $dist_path = plugin_dir_path(__FILE__) . 'frontend/energ-members-dashbaord/dist/';
    $dist_url  = plugin_dir_url(__FILE__) . 'frontend/energ-members-dashbaord/dist/';

    $index_file = $dist_path . 'index.html';

    if (!file_exists($index_file)) {
        error_log('ENERG: index.html not found');
        return;
    }

    $html = file_get_contents($index_file);

    preg_match('/href="(.+\.css)"/', $html, $css_match);
    preg_match('/src="(.+\.js)"/', $html, $js_match);

    if (empty($css_match[1]) || empty($js_match[1])) {
        error_log('ENERG: JS/CSS not found in index.html');
        return;
    }

    wp_enqueue_style(
        'energ-dashboard-style',
        $dist_url . ltrim($css_match[1], '/'),
        [],
        null
    );

    wp_enqueue_script(
        'energ-dashboard-app',
        $dist_url . ltrim($js_match[1], '/'),
        [],
        null,
        true
    );

    wp_add_inline_script(
        'energ-dashboard-app',
        'window.ENERG=' . wp_json_encode([
            'api'  => rest_url('energ/v1'),
            'home' => home_url('/'),
        ]) . ';',
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





add_action('wp_enqueue_scripts', function () {
    if (is_page('dashboard') || str_contains($_SERVER['REQUEST_URI'], '/dashboard')) {
        wp_dequeue_script('elementor-frontend');
        wp_dequeue_script('elementor-common');
        wp_dequeue_script('frontend-modules');
    }
}, 100);


add_shortcode('energ_members_dashboard', function(){
    return '<div id="energ-members-root" data-energ-popup="true"></div>';
});
