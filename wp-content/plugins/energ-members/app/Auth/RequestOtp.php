<?php
namespace Energ\Auth;

use Energ\Services\Mailer;
global $wpdb;

class RequestOtp {

    public function handle($req) {
        $email = sanitize_email($req['email'] ?? '');

        if (!is_email($email)) {
            return new \WP_Error('invalid_email', 'Invalid email');
        }

        $otp = random_int(100000, 999999);
        $hash = password_hash($otp, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', time() + 300);

        $wpdb->delete("{$wpdb->prefix}energ_otps", ['identifier' => $email]);

        $wpdb->insert("{$wpdb->prefix}energ_otps", [
            'identifier' => $email,
            'otp_hash'   => $hash,
            'expires_at'=> $expires,
            'attempts'  => 0,
        ]);

        Mailer::sendOtp($email, $otp);

        return ['success' => true, 'message' => 'OTP sent'];
    }
}
