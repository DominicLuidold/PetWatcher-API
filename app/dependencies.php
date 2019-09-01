<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as DBManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PetWatcher\Application\Validation\Validator;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator as v;

return function (ContainerBuilder $containerBuilder) {
    // Global dependencies
    $containerBuilder->addDefinitions([
        // Database connection
        DBManager::class => function (ContainerInterface $c) {
            $db = new DBManager();
            $db->addConnection($c->get('settings')['db']);

            $db->setAsGlobal();
            $db->bootEloquent();

            return $db;
        },
        // Logger
        LoggerInterface::class => function (ContainerInterface $c) {
            $loggerSettings = $c->get('settings')['logger'];

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($loggerSettings['path'], $loggerSettings['level']));

            return $logger;
        },
        // Validator
        Validator::class => function () {
            return new Validator();
        },
    ]);

    // Custom validation rules
    v::with('PetWatcher\Application\Validation\Rules');
};
