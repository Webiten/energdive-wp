<?php
namespace Energ\Bootstrap;

defined('ABSPATH') || exit;

class Activator {

    public static function activate() {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();

        $users = $wpdb->prefix . 'energ_users';
        $sessions = $wpdb->prefix . 'energ_sessions';
        $otps = $wpdb->prefix . 'energ_otps';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta("
            CREATE TABLE $users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(191) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NULL,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                last_login_at DATETIME NULL
            ) $charset;
        ");

        dbDelta("
            CREATE TABLE $sessions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                token_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                device_info TEXT NULL,
                INDEX(user_id)
            ) $charset;
        ");

        dbDelta("
            CREATE TABLE $otps (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(191) NOT NULL,
                otp_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                attempts INT DEFAULT 0,
                INDEX(identifier)
            ) $charset;
        ");
    }
}
