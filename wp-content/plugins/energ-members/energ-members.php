<?php
/*
Plugin Name: Energ Members (Stable)
Description: Member signup + OTP login + simple profile table (secure rebuild)
Version: 1.0.0
Author: Sankalp
*/

defined('ABSPATH') || exit;

// includes
// require_once plugin_dir_path(__FILE__) . 'inc/otp.php';
// require_once plugin_dir_path(__FILE__) . 'inc/sms-providers.php';
// require_once plugin_dir_path(__FILE__) . 'inc/register-handler.php';
// require_once plugin_dir_path(__FILE__) . 'inc/login-handler.php';
// require_once __DIR__ . "/inc/email-otp-handler.php";
// require_once __DIR__ . "/inc/logout-handler.php";
// require_once plugin_dir_path(__FILE__) . 'inc/session.php';
// require_once plugin_dir_path(__FILE__) . 'inc/tracking.php';
// require_once plugin_dir_path(__FILE__) . 'inc/zoho-sync.php';
// require_once plugin_dir_path(__FILE__) . 'inc/zoho-form-debug.php';
// require_once __DIR__ . '/inc/zoho-forms-handler.php';
// require_once plugin_dir_path(__FILE__) . 'inc/account-handler.php';


// shortcodes
add_shortcode('energ_register', function(){
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/register-form.php';
    return ob_get_clean();
});
add_shortcode('energ_login', function(){
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/login-form.php';
    return ob_get_clean();
});

add_shortcode('energ_complete_registration', function(){
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/complete-registration.php';
    return ob_get_clean();
});

add_shortcode('energ_dashboard', function(){
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/dashboard/layout.php';
    return ob_get_clean();
});

// assets
add_action('wp_enqueue_scripts', function(){

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'energ-js',
        plugin_dir_url(__FILE__) . 'assets/js/energ-members.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script('energ-js', 'ENERG_AJAX', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('energ_nonce'),
    ]);

    wp_enqueue_style(
        'energ-css',
        plugin_dir_url(__FILE__) . 'assets/css/energ-members.css',
        [],
        '1.0'
    );
});


add_action("wp_ajax_energ_save_account", "energ_save_account");
function energ_save_account() {
    global $wpdb;
    $table = $wpdb->prefix . "energ_members";

    $id = intval($_POST["member_id"]);

    $data = [
        "first_name" => sanitize_text_field($_POST["first_name"]),
        "last_name"  => sanitize_text_field($_POST["last_name"]),
        "email"      => sanitize_email($_POST["email"]),
        "dob"        => sanitize_text_field($_POST["dob"]),
        "community"  => sanitize_text_field($_POST["community"]),
        "sub_community" => sanitize_text_field($_POST["sub_community"]),
    ];

    if (!empty($_POST["password"])) {
        $data["password_hash"] = wp_hash_password($_POST["password"]);
    }

    $wpdb->update($table, $data, ["id" => $id]);

    wp_send_json_success("Profile updated successfully!");
}

// activation - create table
register_activation_hook(__FILE__, function(){
    global $wpdb;
    $table = $wpdb->prefix . 'energ_members';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED DEFAULT 0,
        username VARCHAR(100),
        email VARCHAR(200),
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        dob DATE DEFAULT NULL,
        phone VARCHAR(50),
        community VARCHAR(100),
        sub_community VARCHAR(100),
        signup_mode VARCHAR(20),
        otp_code TEXT DEFAULT NULL,
        otp_expires DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email),
        UNIQUE KEY phone (phone)
    ) $charset;";

    // require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});


// register_activation_hook(__FILE__, 'energ_create_reads_table');

global $wpdb;
$table = $wpdb->prefix . "energ_reads";

$charset = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS $table (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id BIGINT UNSIGNED NOT NULL,
    post_id BIGINT UNSIGNED NOT NULL,
    read_at DATETIME NOT NULL,
    PRIMARY KEY(id)
) $charset;";

// require_once ABSPATH . "wp-admin/includes/upgrade.php";
dbDelta($sql);


register_activation_hook(__FILE__, function() {
    global $wpdb;
    $table = $wpdb->prefix . "energ_saved";
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        member_id BIGINT UNSIGNED NOT NULL,
        post_id BIGINT UNSIGNED NOT NULL,
        saved_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY unique_saved (member_id, post_id)
    ) $charset;";

    // require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});
