<?php
declare(strict_types=1);

namespace PetWatcher;

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Monolog\Logger;

$dotenv = Dotenv::create(__DIR__ . '/../../');
$dotenv->load();

return function (ContainerBuilder $containerBuilder) {
    // Global settings
    $containerBuilder->addDefinitions([
        'settings' => [
            // Miscellaneous
            'production' => (bool)getenv('PRODUCTION'),
            'displayErrorDetails' => (bool)getenv('DEBUG'),

            // Image upload
            'upload' => [
                'directory' => getenv('UPLOAD_DIR'),
                'maxSize' => getenv('MAX_SIZE'),
            ],

            // Monolog settings
            'logger' => [
                'name' => 'PetWatcher-API',
                'path' => getenv('LOG_DIR') . 'petwatcher.log',
                'level' => Logger::DEBUG,
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
        ],
    ]);

    // Container cache
    if ((bool)getenv('PRODUCTION')) {
        $containerBuilder->enableCompilation(__DIR__ . '/../../var/cache');
    }
};
