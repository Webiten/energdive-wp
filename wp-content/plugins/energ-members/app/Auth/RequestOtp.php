<?php
namespace Energ\Auth;

use Energ\Services\Mailer;
use WP_Error;

class RequestOtp {

    public function handle($request) {
        global $wpdb;

        $params = $request->get_json_params();
        $email  = sanitize_email($params['email'] ?? '');

        if (!$email || !is_email($email)) {
            return new WP_Error(
                'invalid_email',
                'Invalid email address',
                ['status' => 400]
            );
        }

        // Generate OTP
        try {
            $otp = random_int(100000, 999999);
        } catch (\Exception $e) {
            return new WP_Error(
                'otp_generation_failed',
                'Unable to generate OTP',
                ['status' => 500]
            );
        }

        $hash    = password_hash((string) $otp, PASSWORD_DEFAULT);
        $expires = gmdate('Y-m-d H:i:s', time() + 300); // 5 minutes

        $table = $wpdb->prefix . 'energ_otps';

        // Delete previous OTPs for this email
        $wpdb->delete($table, ['identifier' => $email]);

        // Insert new OTP
        $inserted = $wpdb->insert(
            $table,
            [
                'identifier' => $email,
                'otp_hash'   => $hash,
                'expires_at'=> $expires,
                'attempts'  => 0,
            ],
            ['%s', '%s', '%s', '%d']
        );

        if ($inserted === false) {
            return new WP_Error(
                'db_error',
                'Could not save OTP',
                ['status' => 500]
            );
        }

        // Send OTP via email (Amazon SES via wp_mail)
        $mail = Mailer::sendOtp($email, $otp);

        if (is_wp_error($mail)) {
            return $mail;
        }

        return [
            'success' => true,
            'message' => 'OTP sent to email'
        ];
    }
}
