<?php

return [
    'sqlite' => [
        'driver' => 'sqlite',
        'database' => env('DB_PATH', __DIR__ . '/../database.sqlite'),
        'prefix' => '',
    ],
];