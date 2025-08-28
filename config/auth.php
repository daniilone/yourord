<?php
return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'clients',
    ],
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'clients',
        ],
        'specialist' => [
            'driver' => 'session',
            'provider' => 'specialists',
        ],
        'client' => [
            'driver' => 'session',
            'provider' => 'clients',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],
    'providers' => [
        'clients' => [
            'driver' => 'eloquent',
            'model' => App\Models\Client::class,
        ],
        'specialists' => [
            'driver' => 'eloquent',
            'model' => App\Models\Specialist::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],
    'passwords' => [
        'clients' => [
            'provider' => 'clients',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'specialists' => [
            'provider' => 'specialists',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
    'password_timeout' => 10800,
];
