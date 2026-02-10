<?php

namespace Energ\Services;

use WP_Error;

class Mailer
{

    public static function sendOtp($email, $otp)
    {

        $subject = 'Your EnergDive Login OTP';
        $message = "Your OTP is: {$otp}\n\nThis OTP is valid for 5 minutes.";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        if (!wp_mail($email, $subject, $message, $headers)) {
            return new WP_Error('mail_failed', 'OTP email could not be sent');
        }

        return true;
    }

    public function sendWelcomeEmail($user_id)
    {
        if (!$user_id) return;

        $user = get_userdata($user_id);
        if (!$user) return;

        $email = $user->user_email;
        $name  = $user->display_name ?: $user->user_login;

        $subject = "Welcome to ENERGClub 🎉";

        $message = "
Hi {$name},

You are now officially a member of ENERGClub!

Your account has been successfully activated, and you can now access:
- Exclusive reports  
- Industry insights  
- Community discussions  

Visit your dashboard here:
https://energdive.com/dashboard/

Best regards,  
ENERGDIVE Team
";

        wp_mail($email, $subject, $message);
    }
}
