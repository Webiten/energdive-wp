<?php
namespace Energ\Auth;

use WP_REST_Request;
use WP_REST_Response;

defined('ABSPATH') || exit;

class RequestOtp {

    public static function handle(WP_REST_Request $request): WP_REST_Response {
        global $wpdb;

        $email = sanitize_email($request->get_param('email'));

        if (!is_email($email)) {
            return new WP_REST_Response(['error' => 'Invalid email'], 400);
        }

        $otp = random_int(100000, 999999);
        $hash = password_hash($otp, PASSWORD_DEFAULT);

        $expires = date('Y-m-d H:i:s', time() + 300);

        $table = $wpdb->prefix . 'energdive_otps';

        // Remove old OTPs for this identifier
        $wpdb->delete($table, ['identifier' => $email]);

        $wpdb->insert($table, [
            'identifier' => $email,
            'otp_hash'   => $hash,
            'expires_at'=> $expires,
            'attempts'  => 0
        ]);

        wp_mail(
            $email,
            'Your Energdive OTP',
            "Your OTP is: {$otp}\nValid for 5 minutes."
        );

        return new WP_REST_Response([
            'success' => true,
            'message' => 'OTP sent'
        ]);
    }
}
