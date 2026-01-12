<?php
namespace Energ\Routes;

use Energ\API\AuthController;
use Energ\Auth\Logout;
use Energ\Auth\LogoutAll;

defined('ABSPATH') || exit;

/**
 * This file is loaded INSIDE rest_api_init
 * Do NOT wrap with add_action again
 */

register_rest_route('energ/v1', '/auth/request-otp', [
    'methods'  => 'POST',
    'callback' => [AuthController::class, 'requestOtp'],
    'permission_callback' => '__return_true',
]);

register_rest_route('energ/v1', '/auth/verify-otp', [
    'methods'  => 'POST',
    'callback' => [AuthController::class, 'verifyOtp'],
    'permission_callback' => '__return_true',
]);

register_rest_route('energ/v1', '/auth/refresh-token', [
    'methods'  => 'POST',
    'callback' => [AuthController::class, 'refreshToken'],
    'permission_callback' => '__return_true',
]);

register_rest_route('energ/v1', '/auth/logout', [
    'methods'  => 'POST',
    'callback' => function ($req) {
        return (new Logout)->handle($req);
    },
    'permission_callback' => '__return_true',
]);

register_rest_route('energ/v1', '/auth/logout-all', [
    'methods'  => 'POST',
    'callback' => function ($req) {
        return (new LogoutAll)->handle($req);
    },
    'permission_callback' => function ($req) {
        return \Energ\Middleware\JwtAuth::allow($req);
    },
]);

register_rest_route('energ/v1', '/auth/complete-registration', [
    'methods'  => 'POST',
    'callback' => [\Energ\API\AuthController::class, 'completeRegistration'],
    'permission_callback' => function ($request) {
        return \Energ\Middleware\JwtAuth::allow($request);
    },
]);

