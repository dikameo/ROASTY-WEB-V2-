<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your application. This is
    | useful when building an SPA that will be accessing your API from a
    | different domain.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:5500',
        'http://127.0.0.1:5500',
        'http://127.0.0.1:3000',
        'http://localhost:8000',
        'https://unfollowed-corrin-unorchestrated.ngrok-free.dev',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
