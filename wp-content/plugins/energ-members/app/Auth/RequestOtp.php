<?php
namespace Energ\Auth;

use WP_Error;
use wpdb;

class RequestOtp {

    public function handle($request) {
        global $wpdb;

        $params = $request->get_json_params();
        $email  = sanitize_email($params['email'] ?? '');

        if (!$email || !is_email($email)) {
            return new WP_Error('invalid_email', 'Invalid email', ['status' => 400]);
        }

        // Generate OTP
        $otp = random_int(100000, 999999);
        $hash = password_hash((string)$otp, PASSWORD_DEFAULT);

        // Expiry: 5 minutes
        $expires = gmdate('Y-m-d H:i:s', time() + 300);

        $table = $wpdb->prefix . 'energ_otps';

        // Remove old OTPs for this identifier
        $wpdb->delete($table, ['identifier' => $email]);

        // Insert new OTP
        $wpdb->insert($table, [
            'identifier' => $email,
            'otp_hash'   => $hash,
            'expires_at'=> $expires,
            'attempts'  => 0,
        ]);

        // TEMP: return OTP for testing (REMOVE later)
        return [
            'success' => true,
            'message' => 'OTP generated',
            'debug_otp' => $otp
        ];
    }
}
