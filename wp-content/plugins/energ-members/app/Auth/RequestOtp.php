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

        $params  = $request->get_json_params();
        $input   = trim($params['identifier'] ?? '');
        $context = isset($params['context']) ? trim((string) $params['context']) : 'login'; // login | register_phone

        // Normalize context to known values
        if (!in_array($context, ['login', 'register_phone'], true)) {
            $context = 'login';
        }

        if (!$input) {
            return new WP_Error(
                'missing_identifier',
                'Email or phone is required',
                ['status' => 400]
            );
        }

        /** 🔍 Detect identifier */
        $detected = Identifier::detect($input);
        if (!$detected) {
            return new WP_Error(
                'invalid_identifier',
                'Invalid email or phone number',
                ['status' => 400]
            );
        }

        $type       = $detected['type'];   // email | phone
        $identifier = $detected['value'];  // normalized value (email or digits-only phone)

        /** 🚫 Context safety */
        if ($context === 'register_phone' && $type !== 'phone') {
            return new WP_Error(
                'invalid_context',
                'Phone number required for phone verification',
                ['status' => 400]
            );
        }

        /** ⏱ Rate limit */
        $limit = OtpRateLimiter::check($identifier);
        if (is_wp_error($limit)) {
            return $limit;
        }

        /** 🔢 Generate OTP */
        $otp = random_int(100000, 999999);

        /** 🔐 Store OTP */
        $otpTable = $wpdb->prefix . 'energ_otps';

        // Ensure only one active OTP per identifier
        $wpdb->delete($otpTable, ['identifier' => $identifier]);

        $inserted = $wpdb->insert($otpTable, [
            'identifier' => $identifier,
            'context'    => $context,
            'otp_hash'   => password_hash((string) $otp, PASSWORD_DEFAULT),
            'attempts'   => 0,
            'expires_at' => gmdate('Y-m-d H:i:s', time() + 300), // 5 minutes
        ]);

        if ($inserted === false) {
            return new WP_Error(
                'db_error',
                'Could not create OTP',
                ['status' => 500]
            );
        }

        /** 📤 Send OTP */
        if ($type === 'email') {
            $sent = Mailer::sendOtp($identifier, $otp);
        } else {
            // Most SMS providers require E.164 format: +<countrycode><number>
            $sendTo = $identifier;
            if ($sendTo !== '' && $sendTo[0] !== '+') {
                $sendTo = '+' . $sendTo;
            }

            $sent = SmsService::sendOtp($sendTo, $otp);
        }

        // If sending failed, cleanup stored OTP to avoid stale records
        if (is_wp_error($sent)) {
            $wpdb->delete($otpTable, ['identifier' => $identifier]);
            return $sent;
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'type'    => $type,
            'context' => $context,
        ];
    }
}
