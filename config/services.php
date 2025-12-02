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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    'kkiapay' => [
        'public_key' => env('KKIAPAY_PUBLIC_KEY'),
        'private_key' => env('KKIAPAY_PRIVATE_KEY'),
        'secret_key' => env('KKIAPAY_SECRET_KEY'),
        'webhook_secret' => env('KKIAPAY_WEBHOOK_SECRET'),
    ],

    'sandbox_momo' => [
        'mtn_number' => env('MOMO_MTN_NUMBER', '91234567'),
        'moov_number' => env('MOMO_MOOV_NUMBER', '97234567'),
        'celtiis_number' => env('MOMO_CELTIIS_NUMBER', '99234567'),
    ],

    'sms_gateway' => [
        'api_key' => env('SMS_GATEWAY_API_KEY'),
        'webhook_url' => env('SMS_GATEWAY_WEBHOOK_URL'),
    ],

    'fedapay' => [
        'api_key' => env('FEDAPAY_API_KEY', 'fedapay_api_key_123456789'),
        'public_key' => env('FEDAPAY_PUBLIC_KEY'),
        'secret_key' => env('FEDAPAY_SECRET_KEY'),
        'environment' => env('FEDAPAY_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
    ],

];
