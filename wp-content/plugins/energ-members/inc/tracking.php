<?php

/* ------------------------------
   1. TRACK READ (insert first time)
------------------------------ */
add_action('wp_ajax_energ_track_read', 'energ_track_read');
add_action('wp_ajax_nopriv_energ_track_read', 'energ_track_read');

function energ_track_read() {
    error_log("TRACK_READ FIRED");

    check_ajax_referer('energ_nonce', 'nonce');

    if (empty($_POST['post_id'])) {
        error_log("TRACK_READ ERROR: Missing post_id");
        wp_send_json_error("Missing post_id");
    }

    $session = energ_get_session();
    if (!$session || empty($session['member_id'])) {
        error_log("TRACK_READ ERROR: No session");
        wp_send_json_error("Not logged in");
    }

    global $wpdb;
    $table = $wpdb->prefix . "energ_reads";

    $member_id = intval($session['member_id']);
    $post_id   = intval($_POST['post_id']);

    // Check duplicate
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE member_id=%d AND post_id=%d",
        $member_id, $post_id
    ));

    if (!$exists) {
        $wpdb->insert($table, [
            "member_id" => $member_id,
            "post_id"   => $post_id,
            "read_at"   => current_time('mysql'),
            "progress_percent" => 0
        ]);
        error_log("TRACK_READ INSERTED: $post_id for member $member_id");
    } else {
        error_log("TRACK_READ SKIPPED: Already exists");
    }

    wp_send_json_success("saved");
}


/* ------------------------------
   2. TRACK PROGRESS
------------------------------ */
add_action('wp_ajax_energ_track_progress', 'energ_track_progress');
add_action('wp_ajax_nopriv_energ_track_progress', 'energ_track_progress');

function energ_track_progress() {
    check_ajax_referer('energ_nonce', 'nonce');

    if (empty($_POST['post_id']) || empty($_POST['progress'])) {
        wp_send_json_error();
    }

    $session = energ_get_session();
    if (!$session || empty($session['member_id'])) {
        wp_send_json_error();
    }

    global $wpdb;
    $table = $wpdb->prefix . "energ_reads";

    $member_id = intval($session['member_id']);
    $post_id   = intval($_POST['post_id']);
    $progress  = intval($_POST['progress']);

    // Prevent decreasing
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT progress_percent FROM $table WHERE member_id=%d AND post_id=%d",
        $member_id, $post_id
    ));

    if ($existing !== null) {
        if ($progress > $existing) {
            $wpdb->update(
                $table,
                ['progress_percent' => $progress],
                ['member_id' => $member_id, 'post_id' => $post_id]
            );
        }
    }

    wp_send_json_success();
}

error_log("TRACKING FILE LOADED SUCCESSFULLY");




add_action('wp_ajax_energ_save_article', 'energ_save_article');
add_action('wp_ajax_nopriv_energ_save_article', 'energ_save_article');

function energ_save_article() {

    check_ajax_referer('energ_nonce', 'nonce');

    if (empty($_POST['post_id']))
        wp_send_json_error("Missing post_id");

    $session = energ_get_session();
    if (!$session || empty($session['member_id']))
        wp_send_json_error("Not logged in");

    global $wpdb;
    $table = $wpdb->prefix . "energ_saved";

    $member_id = intval($session['member_id']);
    $post_id   = intval($_POST['post_id']);

    // Check existing
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE member_id=%d AND post_id=%d",
        $member_id, $post_id
    ));

    if ($exists) {
        wp_send_json_error("Already saved");
    }

    // Insert new save
    $wpdb->insert($table, [
        "member_id" => $member_id,
        "post_id"   => $post_id,
        "saved_at"  => current_time('mysql')
    ]);

    wp_send_json_success("saved");
}