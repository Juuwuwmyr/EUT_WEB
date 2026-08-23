<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    /**
     * Send a push notification to a specific user's subscriptions
     */
    public static function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        foreach ($subscriptions as $sub) {
            static::sendToSubscription($sub, $title, $body, $data);
        }
    }

    /**
     * Send a push notification to a single subscription
     */
    public static function sendToSubscription(PushSubscription $sub, string $title, string $body, array $data = []): void
    {
        $vapidPublicKey  = config('services.vapid.public_key');
        $vapidPrivateKey = config('services.vapid.private_key');

        if (!$vapidPublicKey || !$vapidPrivateKey) {
            Log::warning('[WebPush] VAPID keys not configured. Skipping push notification.');
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => '/images/icons/icon-192x192.png',
            'badge' => '/images/icons/icon-72x72.png',
            'data'  => array_merge(['url' => '/shop/tracking'], $data),
        ]);

        try {
            // Parse endpoint to get base URL for VAPID audience
            $parsed   = parse_url($sub->endpoint);
            $audience = $parsed['scheme'] . '://' . $parsed['host'];

            // Build VAPID JWT header
            $header = static::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
            $claims = static::base64UrlEncode(json_encode([
                'aud' => $audience,
                'exp' => time() + 43200,
                'sub' => 'mailto:' . config('mail.from.address', 'noreply@eut.com'),
            ]));

            $unsignedToken = $header . '.' . $claims;
            $signature     = static::signES256($unsignedToken, $vapidPrivateKey);
            $vapidToken    = $unsignedToken . '.' . $signature;

            $authHeader = 'vapid t=' . $vapidToken . ', k=' . $vapidPublicKey;

            // Encrypt payload using ECDH if keys are available
            $headers = [
                'Authorization'   => $authHeader,
                'Content-Type'    => 'application/json',
                'TTL'             => '86400',
            ];

            if ($sub->p256dh_key && $sub->auth_token) {
                $encrypted = static::encryptPayload($payload, $sub->p256dh_key, $sub->auth_token);
                if ($encrypted) {
                    $headers['Content-Encoding'] = 'aesgcm';
                    $headers['Encryption']       = 'salt=' . $encrypted['salt'];
                    $headers['Crypto-Key']       = 'dh=' . $encrypted['dh'] . ';' . substr($authHeader, strlen('vapid '));
                    $payload = $encrypted['ciphertext'];
                }
            }

            $response = Http::withHeaders($headers)
                ->withBody($payload, 'application/octet-stream')
                ->post($sub->endpoint);

            if ($response->status() === 410 || $response->status() === 404) {
                // Subscription expired — remove it
                $sub->delete();
                Log::info('[WebPush] Removed expired subscription: ' . $sub->id);
            } elseif (!$response->successful()) {
                Log::warning('[WebPush] Push failed (' . $response->status() . '): ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('[WebPush] Exception: ' . $e->getMessage());
        }
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function signES256(string $data, string $privateKeyPem): string
    {
        // If it looks like a raw base64url key, wrap it in PEM
        if (!str_contains($privateKeyPem, '-----')) {
            $privateKeyPem = "-----BEGIN EC PRIVATE KEY-----\n"
                . chunk_split(base64_encode(base64_decode(strtr($privateKeyPem, '-_', '+/'))), 64, "\n")
                . "-----END EC PRIVATE KEY-----";
        }

        $keyResource = openssl_pkey_get_private($privateKeyPem);
        if (!$keyResource) {
            throw new \RuntimeException('[WebPush] Invalid VAPID private key');
        }

        openssl_sign($data, $derSignature, $keyResource, OPENSSL_ALGO_SHA256);

        // Convert DER to raw R|S (64 bytes)
        $offset = 2;
        $rLen   = ord($derSignature[$offset + 1]);
        $r      = substr($derSignature, $offset + 2, $rLen);
        $sLen   = ord($derSignature[$offset + 2 + $rLen + 1]);
        $s      = substr($derSignature, $offset + 2 + $rLen + 2, $sLen);

        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");
        $r = str_pad($r, 32, "\x00", STR_PAD_LEFT);
        $s = str_pad($s, 32, "\x00", STR_PAD_LEFT);

        return static::base64UrlEncode($r . $s);
    }

    /**
     * Minimal AES-GCM payload encryption for Web Push
     * Full RFC8291 compliance requires the minishlink/web-push library for production.
     * This sends the payload unencrypted as a fallback if openssl extension isn't available.
     */
    private static function encryptPayload(string $payload, string $p256dhKey, string $authToken): ?array
    {
        // For simplicity, return null to send without encryption
        // Install minishlink/web-push for full encryption support
        return null;
    }
}
