<?php
declare(strict_types=1);

namespace PetWatcher;

use PetWatcher\Controllers\HomeController;
use PetWatcher\Controllers\PetController;
use PetWatcher\Controllers\PetImageController;
use PetWatcher\Controllers\HomeImageController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group(
        '/api', function (Group $group) {
            // API v1
            $group->group(
                '/v1', function (Group $group) {
                    // Pets
                    $group->get('/pets', PetController::class . ':infoAll');
                    $group->post('/pets', PetController::class . ':create');
                    $group->delete('/pets', PetController::class . ':deleteAll');
                    $group->get('/pets/{id}', PetController::class . ':info');
                    $group->put('/pets/{id}', PetController::class . ':update');
                    $group->delete('/pets/{id}', PetController::class . ':delete');

                    // Pet Images
                    $group->group(
                        '/pets/{id}', function (Group $group) {
                            $group->get('/image', PetImageController::class . ':get');
                            $group->post('/image', PetImageController::class . ':add');
                            $group->delete('/image', PetImageController::class . ':delete');
                        }
                    );

                    // Homes
                    $group->get('/homes', HomeController::class . ':infoAll');
                    $group->post('/homes', HomeController::class . ':create');
                    $group->delete('/homes', HomeController::class . ':deleteAll');
                    $group->get('/homes/{id}', HomeController::class . ':info');
                    $group->put('/homes/{id}', HomeController::class . ':update');
                    $group->delete('/homes/{id}', HomeController::class . ':delete');
                    $group->get('/homes/{id}/pets', HomeController::class . ':pets');

                    // Home Images
                    $group->group(
                        '/homes/{id}', function (Group $group) {
                            $group->get('/image', HomeImageController::class . ':get');
                            $group->post('/image', HomeImageController::class . ':add');
                            $group->delete('/image', HomeImageController::class . ':delete');
                        }
                    );
                }
            );
        }
    );
};
