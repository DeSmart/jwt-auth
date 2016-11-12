<?php

return [
    'jwt' => [

        /**
         * Period of time the token will be valid
         */
        'expiration_ttl' => '1 week',

        /**
         * Unique key used to encrypt the token
         */
        'secret_token' => env('API_JWT_TOKEN', 'secret-token'),
    ],

    /**
     * User model classname
     */
    'user_model_class' => App\User::class,
];
