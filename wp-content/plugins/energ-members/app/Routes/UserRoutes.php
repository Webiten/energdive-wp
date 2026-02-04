<?php
defined('ABSPATH') || exit;


// // ####################### //
// // ####################### // 
//       Zoho WEBHOOK
// // ####################### //
// // ####################### //

add_action('rest_api_init', function () {

    register_rest_route('energ/v1', '/zoho-create-user', [
        'methods'  => 'POST',
        'callback' => 'energ_create_user_from_zoho',
        'permission_callback' => '__return_true'
    ]);

});

function energ_create_user_from_zoho(WP_REST_Request $request) {

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

    // Check if already exists
    $exists = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id FROM $table WHERE email = %s",
            $email
        )
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

    // Insert in YOUR existing table
    $wpdb->insert(
        $table,
        [
            'username' => $email,
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'signup_mode' => 'zoho',
            'otp_code' => $token,
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ],
        [
            '%s','%s','%s','%s','%s','%s','%s','%s'
        ]
    );

    return [
        'status' => 'success',
        'message' => 'User created from Zoho',
        'id' => $wpdb->insert_id
    ];
}


