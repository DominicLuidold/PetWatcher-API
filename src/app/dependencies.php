<?php
declare(strict_types=1);

namespace PetWatcher;

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PetWatcher\Validation\Validator;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;

return function (ContainerBuilder $containerBuilder) {
    // Global dependencies
    $containerBuilder->addDefinitions([
        // Database connection
        'db' => function (ContainerInterface $c) {
            $capsule = new Capsule();
            $capsule->addConnection($c->get('settings')['db']);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        },
        // Logger
        'logger' => function (ContainerInterface $c) {
            $loggerSettings = $c->get('settings')['logger'];

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($loggerSettings['path'], $loggerSettings['level']));

            return $logger;
        },
        // Validator
        'validator' => function () {
            return new Validator();
        },
    ]);

    // Custom validation rules
    v::with('PetWatcher\Validation\Rules');
};
