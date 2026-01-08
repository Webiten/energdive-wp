<?php
/*
Plugin Name: Energ Members (Stable)
Description: Member signup + OTP login + JWT auth
Version: 1.0.0
Author: Sankalp
*/

defined('ABSPATH') || exit;

/*
Plugin Name: Energ Members
Version: 1.0.0
*/

spl_autoload_register(function ($class) {
    $prefix = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strpos($class, $prefix) !== 0) return;

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/app/Routes/AuthRoutes.php';
