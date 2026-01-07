<?php
namespace Energ\Auth;

class Jwt {

  public static function generate(array $payload, int $ttl = 900): string {
    $header = base64_encode(json_encode([
      'typ' => 'JWT',
      'alg' => 'HS256'
    ]));

    $payload['iat'] = time();
    $payload['exp'] = time() + $ttl;

    $payload = base64_encode(json_encode($payload));

    $signature = hash_hmac(
      'sha256',
      "$header.$payload",
      AUTH_KEY,
      true
    );

    return "$header.$payload." . base64_encode($signature);
  }

  public static function verify(string $jwt): ?array {
    [$header, $payload, $signature] = explode('.', $jwt);

    $expected = base64_encode(hash_hmac(
      'sha256',
      "$header.$payload",
      AUTH_KEY,
      true
    ));

    if (!hash_equals($expected, $signature)) {
      return null;
    }

    $data = json_decode(base64_decode($payload), true);

    if ($data['exp'] < time()) {
      return null;
    }

    return $data;
  }
}
