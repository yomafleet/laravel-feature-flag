<?php

return [
    'default' => env('FEATURE_FLAG_CONNECTION', 'disabled-toggler'),

    'providers' => [
        'flipt' => [
            'namespace' => env('FLIPT_NAMESPACE', 'default'),
            'host' => env('FLIPT_HOST'),
            'token' => env('FLIPT_TOKEN'),
        ],
        'unleash' => [
            'name' => env('UNLEASH_NAME', 'default'),
            'url' => env('UNLEASH_URL'),
            'id' => env('UNLEASH_ID'),
            'token' => env('UNLEASH_TOKEN'),
        ],
        'disabled-toggler' => [
            'optimistic' => env('DISABLED_TOGGLER_OPTIMISTIC', true)
        ]
    ],

    'disable' => env('FEATURE_FLAG_DISABLE', false),
];
