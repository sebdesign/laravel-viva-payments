<?php

return [
    'viva' => [
        'api_key' => env('VIVA_API_KEY'),
        'merchant_id' => env('VIVA_MERCHANT_ID'),
        'environment' => env('VIVA_ENVIRONMENT', 'production'),
        'client_id' => env('VIVA_CLIENT_ID'),
        'client_secret' => env('VIVA_CLIENT_SECRET'),
        'isv_client_id' => env('VIVA_ISV_CLIENT_ID'),
        'isv_client_secret' => env('VIVA_ISV_CLIENT_SECRET'),
        'isv_partner_id' => env('VIVA_ISV_PARTNER_ID'),
        'isv_partner_api_key' => env('VIVA_ISV_PARTNER_API_KEY'),
        'source_code' => env('VIVA_SOURCE_CODE'),
    ],
];
