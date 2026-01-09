<?php
namespace Energ\Auth;

class Jwt {

    private static function secret() {
        return defined('AUTH_KEY') ? AUTH_KEY : 'energ_fallback_secret';
    }

    public static function issue(array $payload) {

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $issuedAt = time();
        $expire   = $issuedAt + 900; // 15 minutes

        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire
        ]);

        $base64Header  = self::base64UrlEncode(json_encode($header));
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $base64Header . "." . $base64Payload,
            self::secret(),
            true
        );

        $base64Signature = self::base64UrlEncode($signature);

        return [
            'token' => $base64Header . "." . $base64Payload . "." . $base64Signature,
            'expires_in' => 900
        ];
    }

    public static function verify(string $token) {

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $validSig = self::base64UrlEncode(
            hash_hmac(
                'sha256',
                "$header.$payload",
                self::secret(),
                true
            )
        );

        if (!hash_equals($validSig, $signature)) {
            return false;
        }

        $data = json_decode(self::base64UrlDecode($payload), true);

        if (!$data || ($data['exp'] ?? 0) < time()) {
            return false;
        }

        return $data;
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
