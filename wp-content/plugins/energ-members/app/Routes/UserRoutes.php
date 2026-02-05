<?php
namespace Energ\Routes;

use WP_REST_Request;
use WP_Error;

class UserRoutes {

    public static function register() {

        register_rest_route('energ/v1', '/zoho-create-user', [
            'methods'  => 'POST',
            'callback' => [self::class, 'createFromZoho'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function createFromZoho(WP_REST_Request $request) {

        global $wpdb;

        $name  = sanitize_text_field($request->get_param('name'));
        $email = sanitize_email($request->get_param('email'));
        $token = sanitize_text_field($request->get_param('token'));
        $secret = sanitize_text_field($request->get_param('secret'));

        if ($secret !== "ZOHO_ENERGDIVE_SECRET") {
            return new WP_Error('unauthorized', 'Invalid secret', ['status' => 403]);
        }

        if (empty($email)) {
            return new WP_Error('invalid_email', 'Email required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_members';

        // Check existing user
        $exists = $wpdb->get_row(
            $wpdb->prepare("SELECT id FROM $table WHERE email = %s", $email)
        );

        if ($exists) {
            return [
                'status' => 'exists',
                'message' => 'User already exists',
                'id' => $exists->id
            ];
        }

        // Split name
        $parts = explode(' ', $name, 2);
        $first_name = $parts[0];
        $last_name = $parts[1] ?? '';

        // ✅ CORRECT INSERT (Zoho token separate column)
        $wpdb->insert(
            $table,
            [
                'username' => $email,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'signup_mode' => 'zoho',
                'zoho_token' => $token,   // ✅ RIGHT PLACE
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ]
        );

        return [
            'status' => 'success',
            'message' => 'User created from Zoho',
            'id' => $wpdb->insert_id
        ];
    }
}

add_action('rest_api_init', function () {
    \Energ\Routes\UserRoutes::register();
});
