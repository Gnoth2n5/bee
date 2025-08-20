<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayOS Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho PayOS API
    |
    */

    'api' => [
        'base_url' => env('PAYOS_API_URL', 'https://api-merchant.payos.vn'),
        'generate_qr_endpoint' => '/v2/payment-requests',
        'check_status_endpoint' => '/v2/payment-requests/{id}',
        'timeout' => 30,
        'client_id' => env('PAYOS_CLIENT_ID', 'b3968631-65ae-4f8b-9951-b6c1237b1f1e'),
        'api_key' => env('PAYOS_API_KEY', 'ba5c9863-1726-4090-a75e-2f1b5ba13291'),
        'checksum_key' => env('PAYOS_CHECKSUM_KEY', '7f31381b126ce192886088c62c72b039766ce0eaa034a'),
        'use_demo_mode' => true, // Tạm thời dùng demo mode để test
        'webhook_url' => env('PAYOS_WEBHOOK_URL', 'https://bee-food.com/webhook/payos'),
        'payment_url' => env('PAYOS_PAYMENT_URL', 'https://pay.payos.vn/web/'),
    ],

    'default' => [
        'transaction_currency' => '704', // VND
        'country_code' => 'VN',
        'point_of_initiation_method' => '12', // QR Code
    ],

    'banks' => [
        'VCB' => '970436',
        'TCB' => '970407',
        'MB' => '970422',
        'VPB' => '970432',
        'ACB' => '970416',
        'OCB' => '970403',
        'SCB' => '970429',
        'HDB' => '970437',
        'MSB' => '970426',
        'TPB' => '970423',
        'VIB' => '970441',
        'STB' => '970403',
        'BIDV' => '970418',
        'VAB' => '970425',
        'SHB' => '970443',
        'EIB' => '970431',
    ],
];
