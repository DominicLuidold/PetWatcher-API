<?php

namespace PetWatcher;

use Slim\App;

return function (App $app) {

    $app->group('/api', function (App $app) {
        // API v1
        $app->group('/v1', function (App $app) {
            // Pets
            $app->get('/pets', \PetWatcher\Controllers\PetController::class . ':infoAll');
            $app->post('/pets', \PetWatcher\Controllers\PetController::class . ':create');
            $app->delete('/pets', \PetWatcher\Controllers\PetController::class . ':deleteAll');
            $app->get('/pets/{id}', \PetWatcher\Controllers\PetController::class . ':info');
            $app->delete('/pets/{id}', \PetWatcher\Controllers\PetController::class . ':delete');

            // Homes
            $app->get('/homes', \PetWatcher\Controllers\HomeController::class . ':infoAll');
            $app->post('/homes', \PetWatcher\Controllers\HomeController::class . ':create');
            $app->delete('/homes', \PetWatcher\Controllers\HomeController::class . ':deleteAll');
            $app->get('/homes/{id}', \PetWatcher\Controllers\HomeController::class . ':info');
            $app->delete('/homes/{id}', \PetWatcher\Controllers\HomeController::class . ':delete');
            $app->get('/homes/{id}/pets', \PetWatcher\Controllers\HomeController::class . ':pets');
        });
    });
};
