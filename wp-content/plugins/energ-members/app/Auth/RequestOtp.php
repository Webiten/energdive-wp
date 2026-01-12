<?php

namespace Energ\Auth;

use WP_Error;
use Energ\Services\Mailer;
use Energ\Services\SmsService;

class RequestOtp
{
    public function handle($request)
    {
        global $wpdb;

        $params     = $request->get_json_params();
        $identifier = trim($params['identifier'] ?? '');

        if (!$identifier) {
            return new WP_Error(
                'missing_identifier',
                'Email or phone number is required',
                ['status' => 400]
            );
        }

        // 🔍 Detect type
        $isEmail = is_email($identifier);
        $isPhone = preg_match('/^[6-9]\d{9}$/', $identifier);

        if (!$isEmail && !$isPhone) {
            return new WP_Error(
                'invalid_identifier',
                'Enter a valid email or 10-digit phone number',
                ['status' => 400]
            );
        }

        // 🚫 RATE LIMIT CHECK (email OR phone)
        $rateCheck = OtpRateLimiter::check($identifier);
        if (is_wp_error($rateCheck)) {
            return $rateCheck;
        }

        // 🔐 Generate OTP
        try {
            $otp = random_int(100000, 999999);
        } catch (\Exception $e) {
            return new WP_Error(
                'otp_generation_failed',
                'Unable to generate OTP',
                ['status' => 500]
            );
        }

        $otpHash = password_hash((string) $otp, PASSWORD_DEFAULT);
        $expires = gmdate('Y-m-d H:i:s', time() + 300); // 5 minutes

        $otpTable = $wpdb->prefix . 'energ_otps';

        // ♻️ Remove previous OTPs
        $wpdb->delete($otpTable, ['identifier' => $identifier]);

        // 💾 Store OTP
        $inserted = $wpdb->insert(
            $otpTable,
            [
                'identifier' => $identifier,
                'otp_hash'   => $otpHash,
                'expires_at'=> $expires,
                'attempts'  => 0,
            ],
            ['%s', '%s', '%s', '%d']
        );

        if ($inserted === false) {
            return new WP_Error(
                'db_error',
                'Could not store OTP',
                ['status' => 500]
            );
        }

        // 📤 Send OTP
        if ($isEmail) {
            $sent = Mailer::sendOtp($identifier, $otp);
        } else {
            $sent = SmsService::sendOtp($identifier, $otp); // MSG91
        }

        if (is_wp_error($sent)) {
            return $sent;
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully'
        ];
    }
}
