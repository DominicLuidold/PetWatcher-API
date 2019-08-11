<?php
declare(strict_types=1);

namespace PetWatcher;

use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PetWatcher\Validation\Validator;
use Respect\Validation\Validator as v;
use Slim\App;

return function (App $app) {
    /**
     * @var Container $container Instance of dependency container
     */
    $container = $app->getContainer();

    // Database connection
    $container->set(
        'db', function (Container $c) {
            $capsule = new Capsule;
            $capsule->addConnection($c->get('settings')['db']);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        }
    );

    // Logger
    $container->set(
        'logger', function (Container $c) {
            $loggerSettings = $c->get('settings')['logger'];

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($loggerSettings['path'], $loggerSettings['level']));

            return $logger;
        }
    );

    // Validator
    $container->set(
        'validator', function () {
            return new Validator();
        }
    );

    // Custom validation rules
    v::with('PetWatcher\Validation\Rules');
};
