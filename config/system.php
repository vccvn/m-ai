<?php

return [
    '2fa' => [
        'enabled' => env('APP_2FA')
    ],

    'encryption' => [
        'key' => env('APP_ENCRYPTION_KEY')
    ],
    'api' => [
        'ip' => env('API_IP_ADDRESS', '127.0.0.1'),
        'port' => env('NODE_API_PORT', '4444')
    ],
    'modules' => [
        'form_key' => env('MODULE_FORM_KEY', 'id')
    ],
    'gmo' => 'AAA',
    'gmo' => 'AAA',
    'aaa' => 0
];
