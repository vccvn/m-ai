<?php

return [
    'method' => env('BASE_PAYMENT_METHOD', ''),
    'alepay' => [
        'token' => env('ALEPAY_TOKEN_KEY'),
        'checksum' => env('ALEPAY_CHECKSUM'),
        'base_url' => env('ALEPAY_BASE_URL'),
        'asset_url' => env('ALEPAY_ASSET_URL')
    ],
    'nganluong' => [

    ],
    'momo' => []
];
