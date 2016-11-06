<?php

return [
    'jwt' => [
        'expiration_ttl' => '1 week',
        'secret_token' => env('API_JWT_TOKEN', 'secret-token'),
    ],
    'user_model_class' => App\User::class,
];
