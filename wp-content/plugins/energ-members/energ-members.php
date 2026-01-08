<?php
/*
Plugin Name: Energ Members (Stable)
Description: Member signup + OTP login + JWT auth
Version: 1.0.0
Author: Sankalp
*/

defined('ABSPATH') || exit;

/**
 * PSR-4 Autoloader for Energ\
 */
spl_autoload_register(function ($class) {
    $prefix = 'Energ\\';
    $base_dir = __DIR__ . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * LOAD ROUTES (THIS WAS MISSING / BROKEN)
 */
require_once __DIR__ . '/app/Routes/AuthRoutes.php';
