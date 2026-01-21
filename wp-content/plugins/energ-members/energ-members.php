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

    $prefix = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strpos($class, $prefix) !== 0) return;

    $relative = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) require_once $file;
});

/* ===============================
   API ROUTES
================================ */
add_action('rest_api_init', function () {
    require_once __DIR__ . '/app/Routes/AuthRoutes.php';
});

/* ===============================
   SHORTCODES (FORCE LOAD)
================================ */
add_action('init', function () {
    require_once __DIR__ . '/app/Frontend/Shortcodes.php';
    \Energ\Frontend\Shortcodes::register();
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
   FRONTEND ASSETS
================================ */
add_action('wp_enqueue_scripts', function () {

    if (!is_singular()) return;

    global $post;
    if (!$post || empty($post->post_content)) return;

    $needs_dashboard = has_shortcode($post->post_content, 'energ_members_dashboard');
    $needs_auth      = has_shortcode($post->post_content, 'energ_members_auth');

    if (!$needs_dashboard && !$needs_auth) return;

    /* ===== OLD AUTH UI ===== */
    if ($needs_auth) {
        wp_enqueue_style(
            'energ-auth-style',
            plugin_dir_url(__FILE__) . 'assets/css/energ-ui.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'energ-auth-script',
            plugin_dir_url(__FILE__) . 'assets/js/auth.js',
            [],
            '1.0.0',
            true
        );
    }

    /* ===== REACT DASHBOARD ===== */
    if ($needs_dashboard) {

        $dist_path = plugin_dir_path(__FILE__) . 'frontend/energ-members-dashboard/dist/assets/';
        $dist_url  = plugin_dir_url(__FILE__) . 'frontend/energ-members-dashboard/dist/assets/';

        // 🔍 Auto-detect Vite build files
        $js_files  = glob($dist_path . 'index-*.js');
        $css_files = glob($dist_path . 'index-*.css');

        if (!empty($css_files)) {
            $css = $css_files[0];
            wp_enqueue_style(
                'energ-dashboard-style',
                $dist_url . basename($css),
                [],
                filemtime($css)
            );
        }

        if (!empty($js_files)) {
            $js = $js_files[0];
            wp_enqueue_script(
                'energ-dashboard-app',
                $dist_url . basename($js),
                [],
                filemtime($js),
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
    }
});

/* ===============================
   FORCE type="module"
================================ */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'energ-dashboard-app') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);
