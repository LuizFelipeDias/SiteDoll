<?php

return [

    'defaults' => [
        // <<< volte ao padrão 'web'
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        // <<< guarde de sessão padrão
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Opcional: se quiser manter um alias, pode apontar pro mesmo guard
        // 'auth' => [
        //     'driver' => 'session',
        //     'provider' => 'users',
        // ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            // seu model custom com coluna 'senha'
            'model'  => \App\Models\Usuario::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
