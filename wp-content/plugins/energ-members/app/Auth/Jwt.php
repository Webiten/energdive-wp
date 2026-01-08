<?php
namespace Energ\Auth;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

global $wpdb;

class Jwt {

    private static $secret = 'CHANGE_THIS_SECRET';

    public static function issue($email) {
        $now = time();

        $payload = [
            'iss' => site_url(),
            'iat' => $now,
            'exp' => $now + 900,
            'sub' => $email,
        ];

        $access = FirebaseJWT::encode($payload, self::$secret, 'HS256');

        $refresh = bin2hex(random_bytes(32));
        $hash = hash('sha256', $refresh);

        $wpdb->insert("{$wpdb->prefix}energ_sessions", [
            'refresh_token_hash' => $hash,
            'expires_at' => date('Y-m-d H:i:s', $now + 86400 * 7),
        ]);

        return [
            'access_token'  => $access,
            'refresh_token' => $refresh,
        ];
    }

    public static function refresh($req) {
        $token = $req['refresh_token'] ?? '';
        $hash = hash('sha256', $token);

        $row = $GLOBALS['wpdb']->get_row(
            $GLOBALS['wpdb']->prepare(
                "SELECT * FROM {$GLOBALS['wpdb']->prefix}energ_sessions WHERE refresh_token_hash=%s",
                $hash
            )
        );

        if (!$row) {
            return new \WP_Error('invalid_refresh', 'Invalid refresh token');
        }

        return self::issue('refreshed');
    }
}
