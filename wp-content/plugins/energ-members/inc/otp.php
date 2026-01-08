<?php
defined('ABSPATH') || exit;

function energ_generate_otp_code($digits = 6){
    $min = pow(10, $digits-1);
    $max = pow(10, $digits)-1;
    return (string) wp_rand($min, $max);
}

function energ_store_otp_for_member($member, $otp, $lifetime_seconds = 600){
    global $wpdb;
    $table = $wpdb->prefix . 'energ_members';
    $hashed = wp_hash_password($otp);
    $expires = date('Y-m-d H:i:s', time() + $lifetime_seconds);
    $wpdb->update($table, ['otp_code' => $hashed, 'otp_expires' => $expires], ['id' => $member->id]);

    $rate_key = "energ_otp_rate_{$member->id}";
    $count = (int) get_transient($rate_key);
    $count++;
    set_transient($rate_key, $count, 30 * MINUTE_IN_SECONDS);
}

function energ_verify_otp_for_member($member, $input_otp){
    if (empty($member->otp_code) || empty($member->otp_expires)) return false;
    if (strtotime($member->otp_expires) < time()) return false;
    return wp_check_password((string)$input_otp, $member->otp_code);
}

function energ_can_send_otp($member_id){
    $rate_key = "energ_otp_rate_{$member_id}";
    $count = (int) get_transient($rate_key);
    return ($count < 10);
}
