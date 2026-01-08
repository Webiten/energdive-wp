<?php
namespace Energ\Services;

use WP_Error;

class Mailer {

    public static function sendOtp($email, $otp) {

        $subject = 'Your EnergDive Login OTP';
        $message = "Your OTP is: {$otp}\n\nThis OTP is valid for 5 minutes.";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        if (!wp_mail($email, $subject, $message, $headers)) {
            return new WP_Error('mail_failed', 'OTP email could not be sent');
        }

        return true;
    }
}
