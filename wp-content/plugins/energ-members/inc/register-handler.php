<?php
defined('ABSPATH') || exit;

add_action('wp_ajax_nopriv_energ_register','energ_handle_register');
add_action('wp_ajax_energ_register','energ_handle_register');

function energ_handle_register(){
    check_ajax_referer('energ_nonce','nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'energ_members';

    $mode = sanitize_text_field($_POST['mode'] ?? 'quick');
    $username = sanitize_user($_POST['username'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $first = sanitize_text_field($_POST['first_name'] ?? '');
    $last = sanitize_text_field($_POST['last_name'] ?? '');
    $dob = sanitize_text_field($_POST['dob'] ?? null);
    $phone = preg_replace('/\D+/', '', sanitize_text_field($_POST['phone'] ?? ''));
    if (strlen($phone) === 10) $phone = '91' . $phone;
    $community = sanitize_text_field($_POST['community'] ?? '');
    $sub = sanitize_text_field($_POST['sub_community'] ?? '');
    $redirect = esc_url_raw($_POST['redirect'] ?? '');

    if (empty($email)) wp_send_json_error('Email required.');

    // If username or email already exists on WP, block
    if (!empty($username) && username_exists($username)) {
        wp_send_json_error('Username already exists.');
    }
    if (email_exists($email)) {
        wp_send_json_error('Email already exists.');
    }

    // create username if not provided
    if (empty($username)) {
        $username = sanitize_user(explode('@', $email)[0]);
        $username = $username . wp_rand(100,999);
    }

    // create WP user
    $password = wp_generate_password(12, false);
    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        wp_send_json_error('User creation failed: ' . $user_id->get_error_message());
    }

    wp_update_user(['ID'=>$user_id, 'display_name'=>trim("$first $last") ?: $username]);

    // If there's an existing temp member row for this phone, update it; otherwise insert new row
    $existing_member = null;
    if (!empty($phone)) {
        $existing_member = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE REPLACE(REPLACE(phone, '+',''), ' ', '') = %s",
            $phone
        ));
    }

    if ($existing_member) {
        // update the temp row
        $updated = $wpdb->update($table, [
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'first_name' => $first,
            'last_name' => $last,
            'dob' => $dob ?: null,
            'community' => $community,
            'sub_community' => $sub,
            'signup_mode' => $mode,
            'otp_code' => '',
            'otp_expires' => null
        ], ['id' => $existing_member->id]);
        if ($updated === false) {
            wp_send_json_error('Failed to update member record.');
        }
    } else {
        // new insert
        $inserted = $wpdb->insert($table, [
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'first_name' => $first,
            'last_name' => $last,
            'dob' => $dob ?: null,
            'phone' => $phone ?: null,
            'community' => $community,
            'sub_community' => $sub,
            'signup_mode' => $mode
        ]);
        if ($inserted === false) {
            wp_send_json_error('Failed to insert member record.');
        }
    }

    // welcome mail
    $subject = "Welcome to " . get_bloginfo('name');
    $body = "Hi " . ($first ?: $username) . ",\n\nThanks for joining us!";
    wp_mail($email, $subject, $body);

    // log the user in
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    $redirect_to = $redirect ?: site_url('/dashboard'); // default to your requested my-page
    wp_send_json_success(['redirect' => $redirect_to]);
}
