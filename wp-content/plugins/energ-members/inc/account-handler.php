<?php
defined('ABSPATH') || exit;

add_action('wp_ajax_energ_save_account', 'energ_save_account_handler');
add_action('wp_ajax_nopriv_energ_save_account', 'energ_save_account_handler');

function energ_save_account_handler() {
    check_ajax_referer('energ_nonce', 'nonce');
    global $wpdb;

    $table = $wpdb->prefix . 'energ_members';

    /* SECURITY CHECK */
    $session = energ_get_session();
    $posted_id = intval($_POST['member_id'] ?? 0);

    if (!$session || $session['member_id'] != $posted_id) {
        wp_send_json_error("Invalid session");
    }

    $member_id = $session['member_id'];

    /* READ FIELDS FROM AJAX */
    $first  = sanitize_text_field($_POST['first_name'] ?? '');
    $last   = sanitize_text_field($_POST['last_name'] ?? '');
    $email  = sanitize_email($_POST['email'] ?? '');
    $phone  = sanitize_text_field($_POST['phone'] ?? '');

    $salutation = sanitize_text_field($_POST['salutation'] ?? '');
    $country    = sanitize_text_field($_POST['country'] ?? '');

    $organization = sanitize_text_field($_POST['organization'] ?? '');
    $designation  = sanitize_text_field($_POST['designation'] ?? '');

    $industry     = sanitize_text_field($_POST['industry'] ?? '');
    $sub_industry = sanitize_text_field($_POST['sub_industry'] ?? '');

    $community     = $_POST['community'] ?? '';
    $sub_community = $_POST['sub_community'] ?? '';

    /* SANITIZE PHONE */
    $clean_phone = preg_replace('/\D+/', '', $phone);
    if (strlen($clean_phone) === 10) {
        $clean_phone = "91" . $clean_phone;
    }

    /* TERM NAME RESOLVER */
    $term_name = function($value) {
        if (!$value) return '';
        if (is_numeric($value)) {
            $term = get_term(intval($value));
            if ($term && !is_wp_error($term)) return $term->name;
        }
        return sanitize_text_field($value);
    };

    $community_name     = $term_name($community);
    $sub_community_name = $term_name($sub_community);
    $industry_name      = $term_name($industry);
    $sub_industry_name  = $term_name($sub_industry);

    /* BASIC VALIDATION */
    if (!$first || !$last || !$email) {
        wp_send_json_error("First name, last name and email are required.");
    }

    /* FETCH MEMBER ROW */
    $member = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE id=%d", $member_id)
    );

    if (!$member) wp_send_json_error("Member not found");

    /* UPDATE WP USER (IF EXISTS) */
    if ($member->user_id) {
        $wp_user = get_userdata($member->user_id);
        if ($wp_user) {
            // update email if unique
            if ($wp_user->user_email !== $email) {
                $existing = get_user_by('email', $email);
                if (!$existing || $existing->ID == $member->user_id) {
                    wp_update_user([
                        'ID'         => $member->user_id,
                        'user_email' => $email,
                    ]);
                }
            }

            wp_update_user([
                'ID'           => $member->user_id,
                'display_name' => "$first $last"
            ]);
        }
    }

    /* PREPARE DB UPDATE */
    $columns = $wpdb->get_col("DESC $table", 0);

    $update = [];
    if (in_array('first_name', $columns)) $update['first_name'] = $first;
    if (in_array('last_name', $columns)) $update['last_name'] = $last;
    if (in_array('email', $columns)) $update['email'] = $email;
    if (in_array('phone', $columns)) $update['phone'] = $clean_phone;

    if (in_array('salutation', $columns)) $update['salutation'] = $salutation;
    if (in_array('country', $columns)) $update['country'] = $country;

    if (in_array('organization', $columns)) $update['organization'] = $organization;
    if (in_array('designation', $columns)) $update['designation'] = $designation;

    if (in_array('industry', $columns)) $update['industry'] = $industry_name;
    if (in_array('sub_industry', $columns)) $update['sub_industry'] = $sub_industry_name;

    if (in_array('community', $columns)) $update['community'] = $community_name;
    if (in_array('sub_community', $columns)) $update['sub_community'] = $sub_community_name;

    /* SAVE TO DB */
    $res = $wpdb->update($table, $update, ['id' => $member_id]);

    if ($res === false) {
        wp_send_json_error("Failed to save details");
    }

    /* OPTIONALLY SYNC TO ZOHO */
    if (function_exists("energ_sync_member_to_zoho")) {
        try {
            energ_sync_member_to_zoho($member_id);
        } catch (Exception $e) {
            error_log("ZOHO sync failed: " . $e->getMessage());
        }
    }

    wp_send_json_success("Saved");
}
