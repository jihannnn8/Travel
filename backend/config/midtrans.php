<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Midtrans payment gateway
    |
    */

    // Server Key dari Midtrans Dashboard
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    // Client Key dari Midtrans Dashboard
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    // Is Production (true untuk production, false untuk sandbox)
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Is Sanitized (untuk sanitize response)
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    // Is 3DS (untuk 3D Secure)
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];

