<?php

/**
 * PAYMENTS CONFIGS
 */

return [
    'providers' => [
        'mock_api' => [
            'api_url' => env('MOCK_API_URL'),
            'access_key' => env('MOCK_API_ACCESS_KEY'),
            'access_secret_key' => env('MOCK_API_ACCESS_SECRET_KEY'),
            
        ]
    ],
];
