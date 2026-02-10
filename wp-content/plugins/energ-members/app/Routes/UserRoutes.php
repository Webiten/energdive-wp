<?php

namespace Energ\Routes;

use WP_REST_Request;
use WP_Error;

class UserRoutes
{

    public static function register()
    {

        register_rest_route('energ/v1', '/zoho-create-user', [
            'methods'  => 'POST',
            'callback' => [self::class, 'createFromZoho'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function createFromZoho(WP_REST_Request $request)
    {

        global $wpdb;

        $name   = sanitize_text_field($request->get_param('name'));
        $email  = sanitize_email($request->get_param('email'));
        $token  = sanitize_text_field($request->get_param('token'));
        $secret = sanitize_text_field($request->get_param('secret'));

        if ($secret !== "ZOHO_ENERGDIVE_SECRET") {
            return new WP_Error('unauthorized', 'Invalid secret', ['status' => 403]);
        }

        $table = $wpdb->prefix . 'energ_members';

        $exists = $wpdb->get_row(
            $wpdb->prepare("SELECT id FROM $table WHERE email=%s", $email)
        );

        if ($exists) {
            return ['status' => 'exists'];
        }

        $parts = explode(' ', $name, 2);

        $wpdb->insert($table, [
            'email'       => $email,
            'first_name'  => $parts[0],
            'last_name'   => $parts[1] ?? '',
            'signup_mode' => 'zoho',
            'zoho_token'  => $token,
            'status'      => 'pending',
            'created_at'  => current_time('mysql')
        ]);

        return ['status' => 'success'];
    }
}

add_action('rest_api_init', function () {
    UserRoutes::register();
});

use Energ\Auth\Jwt;

add_action('rest_api_init', function () {

    register_rest_route('energ/v1', '/magic-login', [
        'methods'  => 'GET',
        'callback' => 'energ_magic_login',
        'permission_callback' => '__return_true'
    ]);
});

function energ_magic_login()
{

    global $wpdb;

    $token = sanitize_text_field($_GET['token'] ?? '');
    if (!$token) wp_die('Invalid link');

    $table = $wpdb->prefix . 'energ_members';

    $member = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE zoho_token=%s", $token)
    );

    if (!$member) wp_die('Invalid or expired link');

    // Create / get WP user
    if (!email_exists($member->email)) {
        $uid = wp_insert_user([
            'user_login' => $member->email,
            'user_email' => $member->email,
            'user_pass'  => wp_generate_password(),
            'role'       => 'subscriber'
        ]);
    } else {
        $uid = get_user_by('email', $member->email)->ID;
    }

    // Optional WP login
    wp_clear_auth_cookie();
    wp_set_current_user($uid);
    wp_set_auth_cookie($uid, true);

    // 🔐 ISSUE JWT (IMPORTANT: must contain `sub`)
    $jwt = Jwt::issue([
        'sub' => $member->email
    ]);

    // One-time Zoho token
    $wpdb->update(
        $table,
        ['zoho_token' => null, 'status' => 'active'],
        ['id' => $member->id]
    );

    // Redirect to React dashboard
    $jwt = \Energ\Auth\Jwt::issue([
        'sub' => $member->email
    ]);

    wp_redirect(
        "https://stage.energdive.com/dashboard/?token=" . urlencode($jwt['token'])
    );
    exit;
}
