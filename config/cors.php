<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        '*',
        // 'http://trekka.me',
        // 'http://admin.trekka.me',
        // 'http://api.trekka.me',
        // 'http://merchant.trekka.me',
        // 'http://assets.trekka.me',
        // 'http://static.trekka.me',
        // 'http://trekka.vcc.vn',
        // 'http://admin.trekka.vcc.vn',
        // 'http://api.trekka.vcc.vn',
        // 'http://merchant.trekka.vcc.vn',
        // 'http://assets.trekkavcc.vn',
        // 'http://static.trekka.vcc.vn',
        // 'https://trekka.me',
        // 'https://admin.trekka.me',
        // 'https://api.trekka.me',
        // 'https://merchant.trekka.me',
        // 'https://assets.trekka.me',
        // 'https://static.trekka.me',
        // 'https://trekka.vcc.vn',
        // 'https://admin.trekka.vcc.vn',
        // 'https://api.trekka.vcc.vn',
        // 'https://merchant.trekka.vcc.vn',
        // 'https://assets.trekkavcc.vn',
        // 'https://static.trekka.vcc.vn',
        // 'http://localhost',
        // 'http://localhost:3000',
        // 'http://localhost:8000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
