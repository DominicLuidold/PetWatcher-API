<?php

namespace PetWatcher;

use Slim\App;

return function (App $app) {

    $app->group('/api', function (App $app) {
        // API v1
        $app->group('/v1', function (App $app) {
            // Pets
            $app->get('/pets', \PetWatcher\Controllers\PetController::class . ':all');
            $app->post('/pets', \PetWatcher\Controllers\PetController::class . ':create');
            $app->get('/pets/{id}', \PetWatcher\Controllers\PetController::class . ':info');
        });
    });
};
