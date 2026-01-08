<?php
defined('ABSPATH') || exit;
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
|--------------------------------------------------------------------------
| AJAX HOOKS
|--------------------------------------------------------------------------
*/
add_action('wp_ajax_nopriv_energ_send_otp', 'energ_handle_send_otp');
add_action('wp_ajax_nopriv_energ_verify_otp', 'energ_handle_verify_otp');
add_action('wp_ajax_nopriv_energ_password_login', 'energ_password_login_handler');

error_log("OTP_HANDLER_LOADED");


/*
|--------------------------------------------------------------------------
| SEND PHONE OTP
|--------------------------------------------------------------------------
*/
function energ_handle_send_otp()
{
    check_ajax_referer('energ_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'energ_members';

    $identifier = sanitize_text_field($_POST['identifier'] ?? '');
    if (empty($identifier)) wp_send_json_error('Please enter phone.');

    // normalize phone
    $clean = preg_replace('/\D+/', '', $identifier);
    if (strlen($clean) === 10) $clean = '91' . $clean;

    // find member
    $member = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE REPLACE(REPLACE(phone, '+',''), ' ', '') = %s",
        $clean
    ));

    $is_new = false;

    if (!$member) {

        $inserted = $wpdb->insert($table, [
    'user_id'       => 0,
    'email'         => null,
    'first_name'    => null,
    'last_name'     => null,
    'salutation'    => null,
    'phone'         => $clean,
    'country'       => null,
    'organization'  => null,
    'designation'   => null,
    'industry'      => null,
    'sub_industry'  => null,
    'community'     => null,
    'sub_community' => null,
    'signup_mode'   => 'otp_temp',
    'created_at'    => current_time('mysql')
]);

        if ($inserted === false) {
            error_log("ENERG: DB insert failed creating temp phone user.");
            wp_send_json_error('DB error creating temp user.');
        }

        $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $wpdb->insert_id));
        $is_new = true;
    }

    if (!function_exists('energ_can_send_otp') || !energ_can_send_otp($member->id)) {
        wp_send_json_error('Too many OTP requests. Try again later.');
    }

    $otp = energ_generate_otp_code(6);
    energ_store_otp_for_member($member, $otp, 10 * MINUTE_IN_SECONDS);

    error_log("SENDOTP: {$clean} | OTP={$otp}");

    // MSG91 FLOW
    $payload = [
        "template_id" => ENERG_MSG91_TEMPLATE_ID,
        "recipients" => [
            [
                "mobiles" => $clean,
                "var" => $otp
            ]
        ]
    ];

    $response = wp_remote_post("https://control.msg91.com/api/v5/flow?authkey=" . ENERG_MSG91_AUTHKEY, [
        "headers" => [
            "accept" => "application/json",
            "content-type" => "application/json"
        ],
        "body" => wp_json_encode($payload),
        "timeout" => 15
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error("SMS send failed");
    }

    wp_send_json_success([
        "phone" => $member->phone,
        "new_user" => $is_new ? 1 : 0
    ]);
}



/*
|--------------------------------------------------------------------------
| VERIFY PHONE OTP
|--------------------------------------------------------------------------
*/
function energ_handle_verify_otp()
{
    if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'energ_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'energ_members';

    $identifier = sanitize_text_field($_POST['identifier'] ?? '');
    $otp        = sanitize_text_field($_POST['otp'] ?? '');

    if (!$identifier || !$otp) wp_send_json_error('Missing data');

    $clean = preg_replace('/\D+/', '', $identifier);
    if (strlen($clean) === 10) $clean = '91' . $clean;

    $member = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE REPLACE(REPLACE(phone,'+',''),' ','') = %s",
        $clean
    ));

    if (!$member) wp_send_json_error("No user found.");

    if (!energ_verify_otp_for_member($member, $otp)) {
        wp_send_json_error("Invalid or expired OTP.");
    }

    // EXISTING USER (already has email)
    if (!empty($member->email)) {

        // ensure WP user exists
        $wp_user_id = (int)$member->user_id;

        if (!$wp_user_id || !get_userdata($wp_user_id)) {

            $u = get_user_by('email', $member->email);

            if ($u) {
                $wp_user_id = $u->ID;
            } else {
                $safe = sanitize_user(explode("@", $member->email)[0]) . wp_rand(100,999);
                $pw   = wp_generate_password(12, false);

                $wp_user_id = wp_create_user($safe, $pw, $member->email);

                if (is_wp_error($wp_user_id)) {
                    wp_send_json_error("Failed to create WP user.");
                }

                wp_update_user(['ID'=>$wp_user_id,'display_name'=>$member->first_name ?: $safe]);
            }

            $wpdb->update($table, ['user_id'=>$wp_user_id], ['id'=>$member->id]);
        }

        // session
        energ_clear_session();
        energ_set_session($member->id);

        // clear OTP
        $wpdb->update($table, ['otp_code'=>'','otp_expires'=>null], ['id'=>$member->id]);

        wp_send_json_success(["redirect" => site_url("/dashboard")]);
    }

    // NEW USER MUST COMPLETE REGISTRATION
    wp_send_json_success([
        "redirect" => site_url("/complete-registration") . "?phone=" . rawurlencode($member->phone)
    ]);
}



/*
|--------------------------------------------------------------------------
| PASSWORD LOGIN (EMAIL OR PHONE)
|--------------------------------------------------------------------------
*/
function energ_password_login_handler() {
    check_ajax_referer('energ_nonce', 'nonce');

    global $wpdb;
    $members_table = $wpdb->prefix . 'energ_members';

    $identifier = sanitize_text_field($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (!$identifier || !$password) {
        wp_send_json_error("Missing credentials.");
    }

    // Determine identifier type
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $user = get_user_by('email', $identifier);
    } else {
        // phone login
        $clean = preg_replace('/\D+/', '', $identifier);
        if (strlen($clean) === 10) $clean = '91' . $clean;

        $member = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $members_table WHERE REPLACE(REPLACE(phone,'+',''),' ','') = %s",
            $clean
        ));

        $user = null;

        if ($member) {
            if (!empty($member->user_id)) $user = get_userdata($member->user_id);
            elseif (!empty($member->email)) $user = get_user_by('email', $member->email);
        }
    }

    if (!$user) wp_send_json_error("User not found.");

    if (!wp_check_password($password, $user->data->user_pass, $user->ID)) {
        wp_send_json_error("Incorrect password.");
    }

    // WP login
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    // ensure member mapping exists
    $member_row = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $members_table WHERE email = %s",
        $user->user_email
    ));

    if ($member_row) {
        $member_id = $member_row->id;
    } else {
        // create new member entry
        $wpdb->insert($members_table, [
            "user_id" => $user->ID,
            "email" => $user->user_email,
            "username" => $user->user_login,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "signup_mode" => "password_login",
            "created_at" => current_time('mysql')
        ]);
        $member_id = $wpdb->insert_id;
    }

    // session cookie
    energ_set_session($member_id);

    wp_send_json_success(["redirect" => site_url("/dashboard")]);
}