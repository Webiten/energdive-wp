<?php

namespace Energ\Services;

use WP_Error;

class SmsService
{
    public static function sendOtp($phone, $otp)
    {
        // ✅ Config check (ENERG_* constants)
        if (
            !defined('ENERG_MSG91_AUTHKEY') ||
            !defined('ENERG_MSG91_SENDER') ||
            !defined('ENERG_MSG91_TEMPLATE_ID')
        ) {
            return new WP_Error(
                'sms_config_missing',
                'MSG91 config missing',
                ['status' => 500]
            );
        }

        $payload = [
            'template_id' => ENERG_MSG91_TEMPLATE_ID,
            'sender'      => ENERG_MSG91_SENDER,
            'mobiles'     => '91' . $phone,
            'authkey'     => ENERG_MSG91_AUTHKEY,
            'route'       => '4',
            'otp'         => $otp
        ];

        $response = wp_remote_post(
            'https://api.msg91.com/api/v5/otp',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body'    => wp_json_encode($payload),
                'timeout' => 15,
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error(
                'sms_failed',
                'SMS gateway error',
                ['status' => 500]
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($body) || ($body['type'] ?? '') !== 'success') {
            return new WP_Error(
                'sms_failed',
                'OTP could not be sent',
                ['status' => 500, 'response' => $body]
            );
        }

        return true;
    }
}
