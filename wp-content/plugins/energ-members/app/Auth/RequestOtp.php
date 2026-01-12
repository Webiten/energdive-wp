<?php

namespace Energ\Auth;

use WP_Error;
use Energ\Auth\OtpRateLimiter;
use Energ\Helpers\Identifier;
use Energ\Services\Mailer;
use Energ\Services\SmsService;

class RequestOtp
{
    public function handle($request)
    {
        global $wpdb;

        $params     = $request->get_json_params();
        $input      = trim($params['identifier'] ?? '');

        if (!$input) {
            return new WP_Error(
                'missing_identifier',
                'Email or phone is required',
                ['status' => 400]
            );
        }

        /** 🔍 Detect identifier type */
        $detected = Identifier::detect($input);

        if (!$detected) {
            return new WP_Error(
                'invalid_identifier',
                'Invalid email or phone number',
                ['status' => 400]
            );
        }

        $type       = $detected['type'];   // email | phone
        $identifier = $detected['value'];

        /** ⏱ Rate limiting (per identifier + IP) */
        $limitCheck = OtpRateLimiter::check($identifier);
        if (is_wp_error($limitCheck)) {
            return $limitCheck;
        }

        /** 🔢 Generate OTP */
        try {
            $otp = random_int(100000, 999999);
        } catch (\Exception $e) {
            return new WP_Error(
                'otp_generation_failed',
                'Unable to generate OTP',
                ['status' => 500]
            );
        }

        $otpTable = $wpdb->prefix . 'energ_otps';

        // 🔥 Remove old OTPs
        $wpdb->delete($otpTable, ['identifier' => $identifier]);

        // 🔐 Store OTP
        $wpdb->insert(
            $otpTable,
            [
                'identifier' => $identifier,
                'otp_hash'   => password_hash((string) $otp, PASSWORD_DEFAULT),
                'attempts'   => 0,
                'expires_at' => gmdate('Y-m-d H:i:s', time() + 300), // 5 min
            ]
        );

        /** 🚀 Send OTP */
        if ($type === 'email') {
            $sent = Mailer::sendOtp($identifier, $otp);
        } else {
            $sent = SmsService::sendOtp($identifier, $otp);
        }

        if (is_wp_error($sent)) {
            return $sent;
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'type'    => $type,
        ];
    }
}
