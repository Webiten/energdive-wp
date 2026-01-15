<?php

namespace Energ\Services;

use WP_Error;

class SmsService
{
    /**
     * MSG91 expects "mobiles" in format: <countrycode><number> (no +)
     * Example India: 919058500798
     */
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

        // ✅ Normalize phone to digits only
        $digits = preg_replace('/\D+/', '', (string) $phone);
        $len    = strlen($digits);

        if ($len === 10) {
            // India local number → prefix country code
            $mobiles = '91' . $digits;
        } elseif ($len >= 11 && $len <= 15) {
            // Already includes country code (e.g., 91XXXXXXXXXX)
            $mobiles = $digits;
        } else {
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
            'mobiles' => $mobiles,
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

        // Optional: include response for debugging if MSG91 returns errors
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
