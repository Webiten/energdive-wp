<?php
namespace Energ\Services;

use PHPMailer\PHPMailer\PHPMailer;

defined('ABSPATH') || exit;

class Mailer {

    public static function init() {
        add_action('phpmailer_init', [self::class, 'setup']);
    }

    public static function setup(PHPMailer $phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host       = ENERGDIVE_SMTP_HOST;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = ENERGDIVE_SMTP_PORT;
        $phpmailer->Username   = ENERGDIVE_SMTP_USER;
        $phpmailer->Password   = ENERGDIVE_SMTP_PASS;
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->From       = ENERGDIVE_SMTP_FROM;
        $phpmailer->FromName   = ENERGDIVE_SMTP_NAME;
    }
}
