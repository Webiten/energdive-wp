<?php
namespace Energ\API;

use WP_REST_Request;
use WP_REST_Response;

defined('ABSPATH') || exit;

class AuthController {

    public static function register_routes() {

        register_rest_route('energdive/v1', '/auth/request-otp', [
            'methods'  => 'POST',
            'callback' => [self::class, 'request_otp'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function request_otp(WP_REST_Request $request) {

        global $wpdb;
        $email = sanitize_email($request->get_param('email'));

        if (!is_email($email)) {
            return new WP_REST_Response(['error' => 'Invalid email'], 400);
        }

        $otp = random_int(100000, 999999);
        $hash = password_hash($otp, PASSWORD_DEFAULT);

        $expires = date('Y-m-d H:i:s', time() + (5 * 60));

        $wpdb->insert(
            $wpdb->prefix . 'energ_otps',
            [
                'identifier' => $email,
                'otp_hash'   => $hash,
                'expires_at' => $expires
            ]
        );

        // TEMP: Email via wp_mail (SES already configured at server level)
        wp_mail(
            $email,
            'Your Energdive Login OTP',
            "Your OTP is: {$otp}\nValid for 5 minutes."
        );

        return new WP_REST_Response([
            'success' => true,
            'message' => 'OTP sent'
        ], 200);
    }
}
