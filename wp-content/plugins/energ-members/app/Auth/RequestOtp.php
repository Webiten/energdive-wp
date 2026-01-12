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
        $identifier = $params['identifier'] ?? '';

        $detected = energ_detect_identifier($identifier);

        if (!$detected) {
            return new WP_Error(
                'invalid_identifier',
                'Enter valid email or phone number',
                ['status' => 400]
            );
        }

        $type  = $detected['type'];
        $value = $detected['value'];

        // 🔐 Rate limit
        $limit = OtpRateLimiter::check($value);
        if (is_wp_error($limit)) {
            return $limit;
        }

        // 🔢 Generate OTP
        $otp = random_int(100000, 999999);

        $wpdb->delete(
            $wpdb->prefix . 'energ_otps',
            ['identifier' => $value]
        );

        $wpdb->insert(
            $wpdb->prefix . 'energ_otps',
            [
                'identifier' => $value,
                'otp_hash'   => password_hash((string) $otp, PASSWORD_DEFAULT),
                'expires_at' => gmdate('Y-m-d H:i:s', time() + 300),
                'attempts'   => 0,
            ]
        );

        // 📤 SEND OTP
        if ($type === 'email') {
            $sent = Mailer::sendOtp($value, $otp);
        } else {
            $sent = SmsService::sendOtp($value, $otp);
        }

        if (is_wp_error($sent)) {
            return $sent;
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'type'    => $type, // frontend hint
        ];
    }
}
