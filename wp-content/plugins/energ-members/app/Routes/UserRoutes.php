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
        'methods' => 'GET',
        'callback' => function () {

            global $wpdb;

            $token = sanitize_text_field($_GET['token'] ?? '');

            if (!$token) wp_die('Invalid');

            $table = $wpdb->prefix . 'energ_members';

            $member = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table WHERE zoho_token=%s", $token)
            );

            if (!$member) wp_die('Expired');

            // create/get WP user
            if (!email_exists($member->email)) {
                $uid = wp_insert_user([
                    'user_login' => $member->email,
                    'user_email' => $member->email,
                    'user_pass' => wp_generate_password()
                ]);
            } else {
                $uid = get_user_by('email', $member->email)->ID;
            }

            // ISSUE JWT
            $jwt = Jwt::issue([
                'sub' => $member->email
            ]);

            // invalidate token
            $wpdb->update(
                $table,
                ['zoho_token' => null, 'status' => 'active'],
                ['id' => $member->id]
            );

            // REDIRECT TO DASHBOARD WITH JWT
            wp_redirect(
                "https://stage.energdive.com/dashboard/?token=" . $jwt['token']
            );
            exit;
        }
    ]);
});
