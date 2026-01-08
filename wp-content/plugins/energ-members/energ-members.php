<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
