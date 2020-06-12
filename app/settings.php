<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Monolog\Logger;

// Load environment variables from config file
Dotenv::createImmutable(__DIR__ . '/../')->load();

return function (ContainerBuilder $containerBuilder) {
    // Global settings
    $containerBuilder->addDefinitions([
        'settings' => [
            // Miscellaneous
            'production' => ($_SERVER['PRODUCTION'] === 'true'),
            'displayErrorDetails' => ($_SERVER['DEBUG'] === 'true'),

            // Image upload
            'upload' => [
                'directory' => $_SERVER['UPLOAD_DIR'],
                'maxSize' => $_SERVER['MAX_SIZE'],
            ],

            // Monolog settings
            'logger' => [
                'name' => 'PetWatcher-API',
                'path' => $_SERVER['LOG_DIR'] . 'petwatcher.log',
                'level' => (($_SERVER['DEBUG'] === 'true') ? Logger::DEBUG : Logger::INFO),
            ],

            // Database settings
            'db' => [
                'driver' => $_SERVER['DB_DRIVER'],
                'host' => $_SERVER['DB_HOST'],
                'database' => $_SERVER['DB_NAME'],
                'username' => $_SERVER['DB_USER'],
                'password' => $_SERVER['DB_PASSWORD'],
                'charset' => $_SERVER['DB_CHARSET'],
                'collation' => $_SERVER['DB_COLLATION'],
                'prefix' => $_SERVER['DB_PREFIX'],
            ],
        ],
    ]);

    // Container cache
    if ($_SERVER['PRODUCTION'] === 'true') {
        $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
    }
};
