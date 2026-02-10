<?php

namespace Energ\Services;

use WP_Error;

class Mailer
{
    /**
     * ===========================
     *  BRANDED HTML OTP EMAIL
     * ===========================
     */
    public static function sendOtp($email, $otp, $community = 'ENERGClub')
    {
        $subject = 'Your ENERGDIVE Login OTP';

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ENERGDIVE <no-reply@energdive.com>'
        ];

        $message = '
<!DOCTYPE html>
<html>
<body style="margin:0; padding:0; background:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f6f8; padding:40px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" border="0"
style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.08);">

<tr>
<td style="background:#0B1C2D; padding:22px 24px; text-align:center;">
<img src="https://energdive.com/wp-content/uploads/2024/01/energdive-logo.png"
     style="max-height:40px;" alt="ENERGDIVE Logo" />
</td>
</tr>

<tr>
<td style="padding:28px 36px;">
<h2 style="color:#0B1C2D; margin:0 0 10px;">Your Secure Login OTP</h2>

<p style="color:#444; font-size:15px; line-height:1.6;">
You are trying to access <strong>' . $community . '</strong>. Use the OTP below to continue.
</p>

<div style="
background:#f1f3f6;
padding:18px;
text-align:center;
border-radius:8px;
font-size:28px;
letter-spacing:4px;
font-weight:bold;
color:#0B1C2D;
margin:16px 0;
">
' . $otp . '
</div>

<p style="color:#666; font-size:13px;">
This OTP is valid for <strong>5 minutes</strong>. Do not share it with anyone.
</p>
</td>
</tr>

<tr>
<td style="background:#f1f3f6; padding:20px; text-align:center; font-size:12px; color:#555;">
© ' . date("Y") . ' ENERGDIVE — All rights reserved.
</td>
</tr>

</table>

</td></tr>
</table>
</body>
</html>
';

        if (!wp_mail($email, $subject, $message, $headers)) {
            return new WP_Error('mail_failed', 'OTP email could not be sent');
        }

        return true;
    }

    /**
     * =====================================
     *  BRANDED HTML WELCOME EMAIL
     * =====================================
     */
    public function sendWelcomeEmailByAddress($email, $name = 'Member', $community = 'ENERGClub')
    {
        if (empty($email)) return;

        $subject = "Welcome to {$community} 🎉";

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ENERGDIVE <no-reply@energdive.com>'
        ];

        $message = '
<!DOCTYPE html>
<html>
<body style="margin:0; padding:0; background:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f6f8; padding:40px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" border="0"
style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.08);">

<!-- HEADER WITH LOGO -->
<tr>
<td style="background:#0B1C2D; padding:22px 24px; text-align:center;">
<img src="https://energdive.com/wp-content/uploads/2024/01/energdive-logo.png"
     style="max-height:40px;" alt="ENERGDIVE Logo" />
</td>
</tr>

<!-- BADGE -->
<tr>
<td align="center" style="padding:20px 36px 0;">
<span style="
display:inline-block;
background:#E6F4EA;
color:#0F5132;
padding:6px 14px;
border-radius:20px;
font-size:12px;
font-weight:600;
">
✔ Verified Member
</span>
</td>
</tr>

<!-- HERO -->
<tr>
<td style="padding:18px 36px;">
<h2 style="color:#0B1C2D; margin:0 0 8px;">
Hi ' . $name . ',
</h2>

<p style="color:#444; font-size:15px; line-height:1.6;">
Welcome to <strong>' . $community . '</strong> Community — your gateway to trusted energy intelligence, expert insights, and industry collaboration.
</p>
</td>
</tr>

<!-- FEATURES -->
<tr>
<td style="padding:0 36px 20px;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="50%" style="padding:8px 0; font-size:14px; color:#0B1C2D;">• Exclusive Reports</td>
<td width="50%" style="padding:8px 0; font-size:14px; color:#0B1C2D;">• Market Insights</td>
</tr>
<tr>
<td width="50%" style="padding:8px 0; font-size:14px; color:#0B1C2D;">• Industry Analysis</td>
<td width="50%" style="padding:8px 0; font-size:14px; color:#0B1C2D;">• Community Access</td>
</tr>
</table>
</td>
</tr>

<!-- CTA BUTTON -->
<tr>
<td align="center" style="padding:10px 36px 28px;">
<a href="https://energdive.com/dashboard/"
style="
display:inline-block;
background:#0B1C2D;
color:#ffffff;
text-decoration:none;
padding:12px 26px;
border-radius:6px;
font-size:14px;
font-weight:600;
">
Go to Dashboard →
</a>
</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="background:#f1f3f6; padding:20px; text-align:center; font-size:12px; color:#555;">
You are receiving this email because you registered on ENERGDIVE.<br>
© ' . date("Y") . ' ENERGDIVE — All rights reserved.
</td>
</tr>

</table>

</td></tr>
</table>
</body>
</html>
';

        wp_mail($email, $subject, $message, $headers);
    }
}
