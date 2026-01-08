<?php
namespace Energ\Services;

class Mailer {
    public static function sendOtp($email, $otp) {
        wp_mail(
            $email,
            'Your Login OTP',
            "Your OTP is: {$otp}\nValid for 5 minutes."
        );
    }
}
