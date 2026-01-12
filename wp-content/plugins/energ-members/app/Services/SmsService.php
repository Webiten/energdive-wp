<?php

namespace Energ\Services;

use WP_Error;

class SmsService
{
    public static function sendOtp($phone, $otp)
    {
        // ✅ Config check
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

        // ✅ Normalize phone (digits only)
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) !== 10) {
            return new WP_Error(
                'invalid_phone',
                'Invalid phone number',
                ['status' => 400]
            );
        }

        /**
         * ✅ MSG91 SMS API (for SMS Template, NOT OTP API)
         * Template text example (DLT):
         * Dear subscriber, your OTP to login is ##var##. Do not share it.
         */

        $payload = [
            'sender'      => ENERG_MSG91_SENDER,
            'route'       => '4',
            'country'     => '91',
            'sms' => [[
                'message'     => "Dear subscriber, your OTP to login is {$otp}. Do not share it.",
                'to'          => [$phone],
                'template_id'=> ENERG_MSG91_TEMPLATE_ID
            ]]
        ];

        $response = wp_remote_post(
            'https://api.msg91.com/api/v2/sendsms',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'authkey'      => ENERG_MSG91_AUTHKEY,
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
