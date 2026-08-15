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

    'hackclub' => [
        'client_id' => env('HACKCLUB_CLIENT_ID'),
        'client_secret' => env('HACKCLUB_CLIENT_SECRET'),
        'redirect' => env('HACKCLUB_REDIRECT_URI'),
        'base_url' => env('HACKCLUB_BASE_URL', 'https://auth.hackclub.com'),
        'token_url' => env('HACKCLUB_TOKEN_URL', env('HACKCLUB_BASE_URL', 'https://auth.hackclub.com') . '/oauth/token'),
        'scopes' => env('HACKCLUB_SCOPES', 'openid email name profile slack_id'),
    ],   

    'hackatime' => [
        'client_id' => env('HACKATIME_CLIENT_ID'),
        'client_secret' => env('HACKATIME_CLIENT_SECRET'),
        'redirect' => env('HACKATIME_REDIRECT_URI'),
        'base_url' => env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com'),
        'authorize_url' => env('HACKATIME_AUTHORIZE_URL', env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com') . '/oauth/authorize'),
        'token_url' => env('HACKATIME_TOKEN_URL', env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com') . '/oauth/token'),
        'me_url' => env('HACKATIME_ME_URL', env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com') . '/api/v1/authenticated/me'),
        'admin_check_url' => env('HACKATIME_ADMIN_CHECK_URL', env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com') . '/api/admin/v1/check'),
        'profile_url' => env('HACKATIME_PROFILE_URL', env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com') . '/api/v1/authenticated/me'),
        'projects_url' => env('HACKATIME_PROJECTS_URL', env('HACKATIME_BASE_URL', 'https://hackatime.hackclub.com') . '/api/v1/authenticated/projects'),
        'include_archived_projects' => env('HACKATIME_INCLUDE_ARCHIVED_PROJECTS', false),
        'scopes' => env('HACKATIME_SCOPES', 'admin'),
    ],

];
