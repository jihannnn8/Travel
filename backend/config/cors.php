<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
    'api/*',
    'sanctum/csrf-cookie',
    'storage/*',
    'Asset_Travelo/*',
    'web/*'
    ],


    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Allow all origins untuk development (web, mobile, etc)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Enable untuk session cookies

];
