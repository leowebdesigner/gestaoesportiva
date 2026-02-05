<?php

return [
    'base_url' => env('BALLDONTLIE_API_URL', 'https://api.balldontlie.io/v1'),
    'api_key' => env('BALLDONTLIE_API_KEY'),
    'rate_limit' => [
        'requests' => env('BALLDONTLIE_RATE_LIMIT', 30),
        'window' => env('BALLDONTLIE_RATE_WINDOW', 60),
    ],
    'default_season' => env('BALLDONTLIE_DEFAULT_SEASON', 2023),
    'pagination' => [
        'per_page' => 100,
    ],
    'retry' => [
        'times' => 3,
        'sleep' => 1000,
    ],
    'timeout' => 30,
];
