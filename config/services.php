<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    // ── Web Push VAPID ────────────────────────────────────────────────────────
    // Generate keys with: php artisan tinker
    //   $keys = \App\Services\VapidKeyGenerator::generate();
    //   echo $keys['publicKey'] . "\n" . $keys['privateKey'];
    // Or use: npx web-push generate-vapid-keys
    'vapid' => [
        'public_key'  => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
        'subject'     => env('VAPID_SUBJECT', 'mailto:eut@example.com'),
    ],

    // ── PayMongo ─────────────────────────────────────────────────────────────
    // Dashboard: https://dashboard.paymongo.com
    // Test keys start with sk_test_ / pk_test_
    // Live keys start with sk_live_ / pk_live_
    'paymongo' => [
        'public_key'     => env('PAYMONGO_PUBLIC_KEY', ''),
        'secret_key'     => env('PAYMONGO_SECRET_KEY', ''),
        'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET', ''),
    ],

];
