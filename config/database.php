<?php

return [
    "connections" => [
        'pgsql' => [
            'server'    => env('DB_SERVER', 'virtualtest'),
            'user'      => env('DB_USER', 'virtualtest'),
            'password'  => env('DB_PASSWORD', 'virtualtest'),
            'database'  => env('DB_DATABASE', 'virtualtest'),
        ]
    ],
    "default" => env('DB_CONNECTION', "pgsql")
];