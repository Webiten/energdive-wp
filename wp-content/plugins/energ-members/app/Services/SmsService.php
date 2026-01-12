<?php

namespace Energ\Services;

use WP_Error;

class SmsService
{
    public static function sendOtp($phone, $otp)
    {
        if (
            !defined('ENERG_MSG91_AUTHKEY') ||
            !defined('ENERG_MSG91_TEMPLATE_ID')
        ) {
            return new WP_Error(
                'sms_config_missing',
                'MSG91 config missing',
                ['status' => 500]
            );
        }

        // ✅ Normalize phone
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) !== 10) {
            return new WP_Error(
                'invalid_phone',
                'Invalid phone number',
                ['status' => 400]
            );
        }

        /**
         * MSG91 FLOW API (DLT compliant)
         * Template contains: ##var##
         */
        $payload = [
            'flow_id' => ENERG_MSG91_TEMPLATE_ID,
            'sender'  => 'ENERGD',
            'mobiles' => '91' . $phone,
            'var'     => (string) $otp
        ];

        $response = wp_remote_post(
            'https://api.msg91.com/api/v5/flow/',
            [
                'headers' => [
                    'authkey'      => ENERG_MSG91_AUTHKEY,
                    'Content-Type' => 'application/json',
                ],
                'body'    => wp_json_encode($payload),
                'timeout' => 15,
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error(
                'sms_failed',
                'MSG91 request failed',
                ['status' => 500]
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($body['type']) || $body['type'] !== 'success') {
            return new WP_Error(
                'sms_failed',
                'OTP could not be sent',
                ['status' => 500, 'response' => $body]
            );
        }

        return true;
    }
}
