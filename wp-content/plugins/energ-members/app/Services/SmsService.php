<?php

namespace Energ\Services;

use WP_Error;

class SmsService
{
    public static function sendOtp($phone, $otp)
    {
        if (!defined('MSG91_AUTH_KEY')) {
            return new WP_Error(
                'sms_config_missing',
                'MSG91 config missing',
                ['status' => 500]
            );
        }

        $url = 'https://api.msg91.com/api/v5/flow/';

        $payload = [
            'flow_id' => 'YOUR_FLOW_ID', // 🔥 MSG91 flow ID
            'sender'  => MSG91_SENDER_ID,
            'mobiles' => '91' . $phone,
            'otp'     => $otp
        ];

        $args = [
            'headers' => [
                'authkey'      => MSG91_AUTH_KEY,
                'Content-Type' => 'application/json',
            ],
            'body'    => json_encode($payload),
            'timeout' => 20,
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($code !== 200) {
            return new WP_Error(
                'sms_failed',
                'SMS sending failed',
                ['response' => $body]
            );
        }

        return true;
    }
}
