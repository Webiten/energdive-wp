<?php
defined('ABSPATH') || exit;

function energ_send_sms_msg91($phone, $otp) {

    $clean = preg_replace('/\D+/', '', $phone);
    if (strlen($clean) === 10) {
        $clean = "91".$clean;
    }

    $url = "https://api.msg91.com/api/v5/otp";

    $body = [
        "mobile"      => $clean,
        "otp"         => $otp,
        "sender_id"   => ENERG_MSG91_SENDER,
        "template_id" => ENERG_MSG91_TEMPLATE_ID,
    ];

    $args = [
        "headers" => [
            "authkey"       => ENERG_MSG91_AUTHKEY,
            "Content-Type"  => "application/json"
        ],
        "body" => json_encode($body),
        "timeout" => 20
    ];

    $res = wp_remote_post($url, $args);

    error_log("MSG91_SEND_RESPONSE: " . print_r($res, true));

    return $res;
}