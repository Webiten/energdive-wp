<?php
use Energ\Auth\RequestOtp;
use Energ\Auth\VerifyOtp;

add_action('rest_api_init', function () {

  register_rest_route('energdive/v1', '/auth/request-otp', [
    'methods' => 'POST',
    'callback' => [RequestOtp::class, 'handle'],
    'permission_callback' => '__return_true',
  ]);

  register_rest_route('energdive/v1', '/auth/verify-otp', [
    'methods' => 'POST',
    'callback' => [VerifyOtp::class, 'handle'],
    'permission_callback' => '__return_true',
  ]);

});
