<?php

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],
    'guards' => [
        'web' => [
            'driver' => 'admin-sessionless',
            'provider' => null,
        ],
    ],
    'providers' => ['users' => null],
    'passwords' => ['users' => null],
    'password_timeout' => (int) env('AUTH_PASSWORD_TIMEOUT', 10800),
];
