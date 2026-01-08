<?php
// TEMP DEBUG (remove later)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Simple PSR-4 style autoloader for Energ\
spl_autoload_register(function ($class) {
    $prefix = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
/*
Plugin Name: Energ Members (Stable)
Description: Member signup + OTP login + simple profile table (secure rebuild)
Version: 1.0.0
Author: Sankalp
*/

defined('ABSPATH') || exit;

require_once __DIR__ . '/app/Routes/AuthRoutes.php';
require_once __DIR__ . '/app/API/AuthController.php';
require_once __DIR__ . '/app/Auth/RequestOtp.php';
require_once __DIR__ . '/app/Auth/verifyOtp.php';
require_once __DIR__ . '/app/Auth/Jwt.php';
require_once __DIR__ . '/app/Services/Mailer.php';
require_once __DIR__ . '/app/Routes/AuthRoutes.php';
