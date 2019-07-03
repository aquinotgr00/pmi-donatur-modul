<?php

return [
    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'donators',
        ],
    ],

    'providers' => [
        'donators' => [
            'driver' => 'eloquent',
            'model' => BajakLautMalaka\PmiDonatur\Donator::class,
        ],
    ],

    'passwords' => [
        'donators' => [
            'provider' => 'donators',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],
];
