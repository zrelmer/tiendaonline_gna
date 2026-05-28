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

    // Agregamos Recurrente al final del arreglo
    'recurrente' => [
        'secret_key' => env('RECURRENTE_SECRET_KEY'),
        'public_key' => env('RECURRENTE_PUBLIC_KEY'),
        'base_url' => env('RECURRENTE_BASE_URL', 'https://app.recurrente.com/api'),
        'currency' => env('RECURRENTE_CURRENCY', 'GTQ'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_NUMBER'),
        'enabled' => env('TWILIO_WHATSAPP_ENABLED', false),
        'country_code' => env('TWILIO_WHATSAPP_COUNTRY_CODE', '502'),
    ],

];
