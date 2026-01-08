<?php
namespace Energ\Auth;

global $wpdb;

class VerifyOtp {

    public function handle($req) {
        $email = sanitize_email($req['email'] ?? '');
        $otp   = trim($req['otp'] ?? '');

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}energ_otps WHERE identifier=%s ORDER BY id DESC LIMIT 1",
                $email
            )
        );

        if (!$row || strtotime($row->expires_at) < time()) {
            return new \WP_Error('otp_invalid', 'OTP expired');
        }

        if (!password_verify($otp, $row->otp_hash)) {
            return new \WP_Error('otp_invalid', 'Invalid OTP');
        }

        $wpdb->delete("{$wpdb->prefix}energ_otps", ['id' => $row->id]);

        return Jwt::issue($email);
    }
}
