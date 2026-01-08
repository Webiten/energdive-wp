<?php
defined('ABSPATH') || exit;

/**
 * --------------------------------------------------
 * 1) GET SUB TERMS
 * --------------------------------------------------
 */
add_action('wp_ajax_nopriv_energ_get_sub_terms', 'energ_get_sub_terms_ajax');
add_action('wp_ajax_energ_get_sub_terms', 'energ_get_sub_terms_ajax');

function energ_get_sub_terms_ajax(){
    check_ajax_referer('energ_nonce', 'nonce');

    $taxonomy = sanitize_text_field($_POST['taxonomy'] ?? '');
    $parent   = intval($_POST['parent'] ?? 0);

    if (!$taxonomy) wp_send_json_error("taxonomy required");
    if (!taxonomy_exists($taxonomy)) wp_send_json_error("taxonomy_not_found");

    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'parent' => $parent,
        'hide_empty' => false
    ]);

    $data = array_map(fn($t)=>[
        'term_id' => (int)$t->term_id,
        'name'    => $t->name
    ], $terms);

    wp_send_json_success($data);
}

/**
 * --------------------------------------------------
 * 2) SEND EMAIL OTP
 * --------------------------------------------------
 */
add_action('wp_ajax_nopriv_energ_send_email_otp', 'energ_send_email_otp_handler');
add_action('wp_ajax_energ_send_email_otp', 'energ_send_email_otp_handler');

function energ_send_email_otp_handler(){
    check_ajax_referer('energ_nonce', 'nonce');
    global $wpdb;

    $table = $wpdb->prefix . "energ_members";
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');

    if (!$email) wp_send_json_error("Email required.");

    $clean = preg_replace('/\D+/', '', $phone);
    if (strlen($clean) === 10) $clean = "91".$clean;

    $member = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE phone=%s OR email=%s",
        $clean, $email
    ));

    if (!$member) wp_send_json_error("Session expired.");

    $otp = rand(100000,999999);
    $hashed = wp_hash_password($otp);

    $wpdb->update($table,[
        'otp_code' => $hashed,
        'otp_expires' => gmdate("Y-m-d H:i:s", time()+600)
    ],['id'=>$member->id]);

    wp_mail($email,"Your OTP","OTP: $otp (valid 10 min)");

    wp_send_json_success();
}

/**
 * --------------------------------------------------
 * 3) COMPLETE REGISTRATION
 * --------------------------------------------------
 */
add_action('wp_ajax_nopriv_energ_complete_registration','energ_complete_registration_handler');
add_action('wp_ajax_energ_complete_registration','energ_complete_registration_handler');

function energ_complete_registration_handler() {
    check_ajax_referer('energ_nonce', 'nonce');
    global $wpdb;

    $table = $wpdb->prefix . 'energ_members';

    /** REQUIRED FIELDS **/
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $otp   = sanitize_text_field($_POST['email_otp'] ?? '');

    $first = sanitize_text_field($_POST['first_name'] ?? '');
    $last  = sanitize_text_field($_POST['last_name'] ?? '');
    $country = sanitize_text_field($_POST['country'] ?? '');
    $organization = sanitize_text_field($_POST['organization'] ?? '');
    $designation  = sanitize_text_field($_POST['designation'] ?? '');
    $community    = sanitize_text_field($_POST['community'] ?? '');
    $sub_community= sanitize_text_field($_POST['sub_community'] ?? '');
    $industry     = sanitize_text_field($_POST['industry'] ?? '');
    $sub_industry = sanitize_text_field($_POST['sub_industry'] ?? '');
    $salutation   = sanitize_text_field($_POST['salutation'] ?? '');

    /** Required Check (allow empty industry ids if expecting ids; adjust as needed) */
    if (!$email || !$otp || !$first || !$last || !$country || !$organization ||
        !$designation || $community === '' || $sub_community === '' || $industry === '' || $sub_industry === '') {
        wp_send_json_error("All fields required.");
    }

    /** FIND MEMBER BY PHONE ONLY */
    $clean_phone = preg_replace('/\D+/', '', $phone);
    if (strlen($clean_phone) === 10) $clean_phone = '91' . $clean_phone;

    $member = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE REPLACE(REPLACE(phone,'+',''),' ','') = %s", $clean_phone)
    );

    if (!$member) wp_send_json_error("Session expired. Please login again.");

    /** Verify OTP */
    if (!energ_verify_otp_for_member($member, $otp)) {
        wp_send_json_error("Invalid or expired email OTP.");
    }

    /** CREATE WP USER IF NEEDED */
    $wp_user_id = (int)$member->user_id;

    if (!$wp_user_id) {
        $existing = get_user_by('email', $email);

        if ($existing) {
            $wp_user_id = $existing->ID;
        } else {
            $username = sanitize_user(explode('@', $email)[0]) . wp_rand(100,999);
            $password = wp_generate_password(12, false);

            $wp_user_id = wp_create_user($username, $password, $email);

            if (is_wp_error($wp_user_id)) {
                wp_send_json_error("Failed to create WP user.");
            }

            wp_update_user([
                'ID' => $wp_user_id,
                'display_name' => "$first $last"
            ]);
        }
    }

    /** UPDATE MEMBER RECORD (prepare data array only) */
    $update_data = [
        'user_id' => $wp_user_id,
        'email' => $email,
        'first_name' => $first,
        'last_name' => $last,
        'phone' => $clean_phone,
        'country' => $country,
        'organization' => $organization,
        'designation' => $designation,
        'community' => $community,
        'sub_community' => $sub_community,
        'industry' => $industry,
        'sub_industry' => $sub_industry,
        'signup_mode' => 'completed',
        'otp_code' => '',
        'otp_expires' => null
    ];

    // include salutation column only if DB has it
    $columns = $wpdb->get_col("DESC {$table}", 0);
    if (in_array('salutation', $columns, true)) {
        $update_data['salutation'] = $salutation;
    }

    $updated = $wpdb->update($table, $update_data, ['id' => $member->id]);

    if ($updated === false) {
        error_log("ENERG: DB error updating member id {$member->id} - " . $wpdb->last_error);
        wp_send_json_error("Failed to update member record.");
    }

    /* ----------------------------------------------------------
     *  ZOHO SYNC (non-blocking best-effort)
     * ---------------------------------------------------------- */
    if (function_exists('energ_sync_member_to_zoho')) {
        // run but don't block response — call in background via wp_remote_post to admin-ajax or keep sync (here we call sync but it's okay)
        try {
            energ_sync_member_to_zoho($member->id);
        } catch (Exception $e) {
            error_log("ENERG: Zoho sync exception: " . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     *  SEND WELCOME EMAIL AFTER SETTING WP COOKIES (so user stays logged in)
     * ---------------------------------------------------------- */

    // Clear any previous session and set our own session cookie
    energ_clear_session();
    energ_set_session($member->id);

    // Ensure WP auth cookies are set so subsequent page loads treat user as logged in
    if ($wp_user_id) {
        wp_set_current_user($wp_user_id);
        wp_set_auth_cookie($wp_user_id, true);
    }

    // Send welcome mail (non-blocking preferred)
    wp_mail(
        $email,
        "Welcome to ENERGDIVE",
        "Hi $first,\n\nYour ENERGDIVE member account is now active.\nDashboard: " . site_url('/dashboard') . "\n\nRegards,\nENERGDIVE Team"
    );

    // Finally send success with redirect
    wp_send_json_success(['redirect' => site_url('/dashboard')]);
}


/**
 * --------------------------------------------------
 * 4) ZOHO SYNC FUNCTION
 * --------------------------------------------------
 */
if (function_exists('energ_sync_member_to_zoho')) {
    energ_sync_member_to_zoho($member->id);
}

/**
 * --------------------------------------------------
 * 5) VERIFY EMAIL OTP
 * --------------------------------------------------
 */
add_action('wp_ajax_nopriv_energ_verify_email_otp','energ_verify_email_otp_handler');
add_action('wp_ajax_energ_verify_email_otp','energ_verify_email_otp_handler');

function energ_verify_email_otp_handler(){
    check_ajax_referer('energ_nonce','nonce');
    global $wpdb;

    $email = sanitize_email($_POST['email']);
    $otp   = sanitize_text_field($_POST['otp']);
    $phone = sanitize_text_field($_POST['phone']);

    $clean = preg_replace('/\D+/', '', $phone);
    if (strlen($clean)==10) $clean="91".$clean;

    $table = $wpdb->prefix."energ_members";

    $member = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE phone=%s OR email=%s",
        $clean,$email
    ));

    if (!$member) wp_send_json_error("Session expired");

    if (!energ_verify_otp_for_member($member,$otp)){
        wp_send_json_error("Incorrect OTP");
    }

    wp_send_json_success("verified");
}
