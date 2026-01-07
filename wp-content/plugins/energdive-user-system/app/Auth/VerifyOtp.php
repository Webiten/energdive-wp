<?php
namespace Energ\Auth;

use WP_REST_Request;
use WP_REST_Response;
use Energ\Auth\Jwt;

class VerifyOtp {

  public static function handle(WP_REST_Request $req): WP_REST_Response {
    global $wpdb;

    $identifier = sanitize_email($req->get_param('email'));
    $otp = $req->get_param('otp');

    if (!$identifier || !$otp) {
      return new WP_REST_Response(['error' => 'Invalid input'], 400);
    }

    $table = $wpdb->prefix . 'energdive_otps';

    $row = $wpdb->get_row(
      $wpdb->prepare(
        "SELECT * FROM $table WHERE identifier = %s",
        $identifier
      )
    );

    if (!$row) {
      return new WP_REST_Response(['error' => 'OTP not found'], 401);
    }

    if (strtotime($row->expires_at) < time()) {
      return new WP_REST_Response(['error' => 'OTP expired'], 401);
    }

    if (!password_verify($otp, $row->otp_hash)) {
      $wpdb->query(
        $wpdb->prepare(
          "UPDATE $table SET attempts = attempts + 1 WHERE id = %d",
          $row->id
        )
      );

      return new WP_REST_Response(['error' => 'Invalid OTP'], 401);
    }

    // ✅ OTP valid → delete
    $wpdb->delete($table, ['id' => $row->id]);

    // 👤 User create / fetch
    $user = get_user_by('email', $identifier);

    if (!$user) {
      $user_id = wp_create_user(
        $identifier,
        wp_generate_password(),
        $identifier
      );
      $user = get_user_by('id', $user_id);
    }

    // 🔐 JWT
    $token = Jwt::generate([
      'uid' => $user->ID,
      'email' => $user->user_email
    ]);

    return new WP_REST_Response([
      'success' => true,
      'token' => $token,
      'user' => [
        'id' => $user->ID,
        'email' => $user->user_email
      ]
    ]);
  }
}
