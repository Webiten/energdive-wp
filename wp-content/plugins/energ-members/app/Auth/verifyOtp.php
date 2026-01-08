<?php
namespace Energ\Auth;

use WP_Error;

class VerifyOtp {

    public function handle($request) {
        global $wpdb;

        $params = $request->get_json_params();
        $email  = sanitize_email($params['email'] ?? '');
        $otp    = trim($params['otp'] ?? '');

        if (!$email || !$otp) {
            return new WP_Error('invalid_input', 'Email & OTP required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'energ_otps';

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE identifier = %s LIMIT 1",
                $email
            )
        );

        if (!$row) {
            return new WP_Error('otp_not_found', 'OTP not found', ['status' => 400]);
        }

        if (strtotime($row->expires_at) < time()) {
            return new WP_Error('otp_expired', 'OTP expired', ['status' => 400]);
        }

        if (!password_verify($otp, $row->otp_hash)) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $table SET attempts = attempts + 1 WHERE id = %d",
                    $row->id
                )
            );
            return new WP_Error('invalid_otp', 'Invalid OTP', ['status' => 400]);
        }

        // OTP valid → delete it
        $wpdb->delete($table, ['id' => $row->id]);

        return [
            'success' => true,
            'message' => 'OTP verified'
        ];
    }
}
