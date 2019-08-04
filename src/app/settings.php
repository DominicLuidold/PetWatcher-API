<?php

namespace PetWatcher;

$dotenv = \Dotenv\Dotenv::create(__DIR__ . '/../../');
$dotenv->load();

return [
    'settings' => [
        'displayErrorDetails' => getenv('DEBUG'),

        // Image upload
        'upload' => [
            'directory' => getenv('UPLOAD_DIR'),
            'maxSize' => getenv('MAX_SIZE'),
        ],

        // Monolog settings
        'logger' => [
            'name' => 'PetWatcher-API',
            'path' => getenv('LOG_DIR') . 'petwatcher.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database settings
        'db' => [
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_NAME'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => getenv('DB_CHARSET'),
            'collation' => getenv('DB_COLLATION'),
            'prefix' => getenv('DB_PREFIX'),
        ],
    ]
];
