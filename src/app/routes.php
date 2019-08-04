<?php

namespace PetWatcher;

use PetWatcher\Controllers\HomeController;
use PetWatcher\Controllers\PetController;
use PetWatcher\Controllers\PetImageController;
use PetWatcher\Controllers\HomeImageController;
use Slim\App;

return function (App $app) {

    $app->group('/api', function (App $app) {
        // API v1
        $app->group('/v1', function (App $app) {
            // Pets
            $app->get('/pets', PetController::class . ':infoAll');
            $app->post('/pets', PetController::class . ':create');
            $app->delete('/pets', PetController::class . ':deleteAll');
            $app->get('/pets/{id}', PetController::class . ':info');
            $app->put('/pets/{id}', PetController::class . ':update');
            $app->delete('/pets/{id}', PetController::class . ':delete');

            // Pet Images
            $app->group('/pets/{id}', function (App $app) {
                $app->get('/image', PetImageController::class . ':get');
                $app->post('/image', PetImageController::class . ':add');
                $app->delete('/image', PetImageController::class . ':delete');
            });

            // Homes
            $app->get('/homes', HomeController::class . ':infoAll');
            $app->post('/homes', HomeController::class . ':create');
            $app->delete('/homes', HomeController::class . ':deleteAll');
            $app->get('/homes/{id}', HomeController::class . ':info');
            $app->put('/homes/{id}', HomeController::class . ':update');
            $app->delete('/homes/{id}', HomeController::class . ':delete');
            $app->get('/homes/{id}/pets', HomeController::class . ':pets');

            // Home Images
            $app->group('/homes/{id}', function (App $app) {
                $app->get('/image', HomeImageController::class . ':get');
                $app->post('/image', HomeImageController::class . ':add');
                $app->delete('/image', HomeImageController::class . ':delete');
            });
        });
    });
};
