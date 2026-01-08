<?php
namespace Energ\Auth;

class Jwt {

    private static function secret() {
        return defined('AUTH_KEY') ? AUTH_KEY : 'change-me';
    }

    public static function generate(array $payload, int $ttlSeconds): string {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['iat'] = time();
        $payload['exp'] = time() + $ttlSeconds;

        $base64Header  = self::b64(json_encode($header));
        $base64Payload = self::b64(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $base64Header . '.' . $base64Payload,
            self::secret(),
            true
        );

        return $base64Header . '.' . $base64Payload . '.' . self::b64($signature);
    }

    public static function verify(string $jwt) {
        [$h, $p, $s] = explode('.', $jwt);
        $expected = self::b64(hash_hmac(
            'sha256',
            "$h.$p",
            self::secret(),
            true
        ));

        if (!hash_equals($expected, $s)) return false;

        $payload = json_decode(self::ub64($p), true);
        if (($payload['exp'] ?? 0) < time()) return false;

        return $payload;
    }

    private static function b64($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    private static function ub64($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
