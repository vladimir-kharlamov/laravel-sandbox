<?php
return [
    'default' => [
        'host' => env('RABBITMQ_HOST', '127.0.0.1'),
        'port' => env('RABBITMQ_PORT', 15672),
        'user' => env('RABBITMQ_USERNAME', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => '/',
        'queue' => 'default',
        'exchange' => 'default',
        'consumer_tag' => 'consumer',
        'timeout' => 0,
        'persistent' => false,
    ],
];
