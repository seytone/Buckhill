<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTest extends TestCase
{
    public function test_token_is_encoded(): string
    {
        $timestamp = new DateTimeImmutable();
        $issued_at = $timestamp->getTimestamp();
        $expires_at = $timestamp->modify('+3 hour')->getTimestamp();

        // define jwt token claims
        $payload = [
            'iss' => 'SERVER_NAME',
            'sub' => 'user_uuid',
            'jti' => base64_encode(random_bytes(16)),
            'iat' => $issued_at,
            'exp' => $expires_at,
            'data' => [
                'user_uuid' => 'user_uuid',
                'user_email' => 'user_email',
                'user_is_admin' => 0,
            ]
        ];

        $key = $this->getKeyValue('private');

        // use private key to encode and generate token
        $token = JWT::encode($payload, $key, 'RS256');

        $this->assertNotEmpty($token);

        return $token;
    }

    public function test_token_is_decoded(): void
    {
        $key = $this->getKeyValue('public');
        $jwt = $this->test_token_is_encoded();

        // use public key to decode the token
        $token = JWT::decode($jwt, new Key($key, 'RS256'));

        $this->assertNotEmpty($token);
    }

    protected function getKeyValue($key): string
    {
        if ($key == 'private')
        {
            return <<<KEY
            -----BEGIN RSA PRIVATE KEY-----
            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
            -----END RSA PRIVATE KEY-----
            KEY;
        } else {
            return <<<KEY
            -----BEGIN PUBLIC KEY-----
            MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEArkO9fjoQoTij6YjELdJf
            VfAbCBh25/JsQA0NDw5EK3n89N4umCXTXsA8HTgUVGjNuY9NeLC9URTmNWrrS3lh
            /ArL9pOQ6Hf+LZ+vqVQU/udQnChIKUuZtiTT23nfn7BXorPD10OJgdCRF1CCEywF
            mlB/j8oCOFeEYMZrtpPYvafNx+G97sw4KUahXwzUBVIC38+cTdKgn45skDc2ebjX
            PLfPDXa4lp62O+/tmFY7Fholyt2gF2VWIlL8WtpQX/QCLCmXaOdAPTk9WVHtka0o
            wRHmMOLoI97fIUm3KetHi0DJGfqT0FnlF/Q/YE7Bfd1tuaITHHgHviVGn1CZvSHx
            MeHK2rWtRBP3ugs+jlugitiVMwBqFQIDN9zwPOgRW5Vu5NkLgSWyYtUkDIoeFCYp
            ZwEaTSQkODm3u/tl8wzy0D3XyUdPKFkvMRh+gzBs6rkx1USMJ+x/ZjZFIT/pKKnO
            1vtchwN6WAYvTY+LIPZklaEXzW4Ew2XD1kSzEReDzq6SkH5F/gCnM72HU867QHE0
            vQI/KJUW9GAutwgUQcw7Jm6Lj4etNMEW0T4H7Rlmkn/tZBeiUuxqZqpRQ9oGKzab
            Nnto6rXoPd7+inbSA709ecWEW7ZS8529RoG/vmIMYaZA174qHwH6R0UTh2FfScao
            m0QpH4s5oLrmsga3XWyTtAUCAwEAAQ==
            -----END PUBLIC KEY-----
            KEY;
        }
    }
}

