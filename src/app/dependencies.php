<?php

namespace PetWatcher;

use Respect\Validation\Validator as v;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // Database connection
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container["settings"]["db"]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    $container['db'] = function () use ($capsule) {
        return $capsule;
    };

    // Monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];

        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // Validator
    $container['validator'] = function () {
        return new \PetWatcher\Validation\Validator();
    };

    // Custom validation rules
    v::with('PetWatcher\Validation\Rules');
};
