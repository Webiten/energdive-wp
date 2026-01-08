<?php
defined('ABSPATH') || exit;

/**
 * ENERGDIVE SESSION HANDLER — FINAL STABLE VERSION
 */

if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
if (!defined('DONOTCACHEOBJECT')) define('DONOTCACHEOBJECT', true);
if (!defined('DONOTMINIFY')) define('DONOTMINIFY', true);

if (!ob_get_level()) {
    ob_start(); // start very early
}

function energ_cookie_domain() {
    return '.energdive.com';
}

function energ_set_session($member_id) {

    if (empty($member_id)) return;

    $val = base64_encode(json_encode([
        "member_id" => (int)$member_id,
        "t" => time()
    ]));

    $domain  = energ_cookie_domain();
    $expires = gmdate('D, d M Y H:i:s T', time() + 86400*30);

    $cookie = "energ_user={$val}; Path=/; Domain={$domain}; Expires={$expires}; HttpOnly; Secure; SameSite=None;";
    @header("Set-Cookie: $cookie", false);

    $_COOKIE['energ_user'] = $val;
}

function energ_get_session() {

    if (empty($_COOKIE['energ_user'])) return false;

    $decoded = json_decode(base64_decode($_COOKIE['energ_user']), true);
    if (!$decoded || empty($decoded['member_id'])) return false;

    return $decoded;
}

function energ_clear_session() {

    $domain  = energ_cookie_domain();
    $expires = gmdate('D, d M Y H:i:s T', time() - 3600);

    $cookie = "energ_user=; Path=/; Domain={$domain}; Expires={$expires}; HttpOnly; Secure; SameSite=None;";
    @header("Set-Cookie: $cookie", false);

    unset($_COOKIE['energ_user']);
}

add_action('shutdown', function () {
    while (ob_get_level()) {
        @ob_end_clean();
    }
});

add_filter('show_admin_bar', function($show){
    if ( current_user_can('administrator') || current_user_can('editor') ) {
        return true; // admin and editor can see admin bar
    }
    return false; // others cannot
});


add_action('admin_init', function() {
    if ( !current_user_can('administrator') && !current_user_can('editor') ) {
        
        // Allow AJAX otherwise forms break
        if ( wp_doing_ajax() ) return;

        wp_redirect( site_url('/dashboard') );
        exit;
    }
});
