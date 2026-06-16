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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'weather' => [
        'url' => env('WEATHER_API_URL'),
        'api_key' => env('WEATHER_API_KEY'),
    ],
    'quotes' => [
        'url' => env('QUOTES_API_URL'),
        'api_key' => env('QUOTES_API_KEY'),
    ],
    'line' => [
        'client_id' => env('LINE_CLIENT_ID'),
        'client_secret' => env('LINE_CLIENT_SECRET'),
        'redirect' => env('LINE_REDIRECT_URL'),
        'scopes' => ['profile', 'openid', 'email'],
    ],
    'freee' => [
        'client_id' => env('FREEE_CLIENT_ID'),
        'client_secret' => env('FREEE_CLIENT_SECRET'),
        'redirect_uri' => env('FREEE_REDIRECT_URI'),
        'auth_url' => env('FREEE_AUTH_URL', 'https://accounts.secure.freee.co.jp/public_api/authorize'),
        'token_url' => env('FREEE_TOKEN_URL', 'https://accounts.secure.freee.co.jp/public_api/token'),
        'api_base_url' => env('FREEE_API_BASE_URL', 'https://api.freee.co.jp'),
        'scopes' => 'write read',
        'company_id' => env('FREEE_COMPANY_ID', 0),
        'invoice_template_id' => env('FREEE_INVOICE_TEMPLATE_ID', 0),
    ],

];
