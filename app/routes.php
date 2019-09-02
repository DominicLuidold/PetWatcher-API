<?php
declare(strict_types=1);

use PetWatcher\Application\Actions\Home\CreateHomeAction;
use PetWatcher\Application\Actions\Home\DeleteHomeAction;
use PetWatcher\Application\Actions\Home\DeleteAllHomesAction;
use PetWatcher\Application\Actions\Home\Image\AddHomeImageAction;
use PetWatcher\Application\Actions\Home\Image\DeleteHomeImageAction;
use PetWatcher\Application\Actions\Home\Image\ViewHomeImageAction;
use PetWatcher\Application\Actions\Home\ListHomePetsAction;
use PetWatcher\Application\Actions\Home\ListHomesAction;
use PetWatcher\Application\Actions\Home\UpdateHomeAction;
use PetWatcher\Application\Actions\Home\ViewHomeAction;
use PetWatcher\Application\Actions\Pet\CreatePetAction;
use PetWatcher\Application\Actions\Pet\DeletePetAction;
use PetWatcher\Application\Actions\Pet\DeleteAllPetsAction;
use PetWatcher\Application\Actions\Pet\Image\AddPetImageAction;
use PetWatcher\Application\Actions\Pet\Image\DeletePetImageAction;
use PetWatcher\Application\Actions\Pet\Image\ViewPetImageAction;
use PetWatcher\Application\Actions\Pet\ListPetsAction;
use PetWatcher\Application\Actions\Pet\UpdatePetAction;
use PetWatcher\Application\Actions\Pet\ViewPetAction;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/api', function (Group $group) {
        // API v1
        $group->group('/v1', function (Group $group) {
            // Pets
            $group->get('/pets', ListPetsAction::class)->setName('list-pets');
            $group->post('/pets', CreatePetAction::class)->setName('create-pet');
            $group->delete('/pets', DeleteAllPetsAction::class)->setName('delete-pets');
            $group->get('/pets/{id}', ViewPetAction::class)->setName('view-pet');
            $group->put('/pets/{id}', UpdatePetAction::class)->setName('update-pet');
            $group->delete('/pets/{id}', DeletePetAction::class)->setName('delete-pet');

            // Pet Images
            $group->group('/pets/{id}', function (Group $group) {
                $group->get('/image', ViewPetImageAction::class)->setName('view-pet-image');
                $group->post('/image', AddPetImageAction::class)->setName('add-pet-image');
                $group->delete('/image', DeletePetImageAction::class)->setName('delete-pet-image');
            });

            // Homes
            $group->get('/homes', ListHomesAction::class)->setName('list-homes');
            $group->post('/homes', CreateHomeAction::class)->setName('create-home');
            $group->delete('/homes', DeleteAllHomesAction::class)->setName('delete-homes');
            $group->get('/homes/{id}', ViewHomeAction::class)->setName('view-home');
            $group->put('/homes/{id}', UpdateHomeAction::class)->setName('update-home');
            $group->delete('/homes/{id}', DeleteHomeAction::class)->setName('delete-home');
            $group->get('/homes/{id}/pets', ListHomePetsAction::class)->setName('list-home-pets');

            // Home Images
            $group->group('/homes/{id}', function (Group $group) {
                $group->get('/image', ViewHomeImageAction::class)->setName('view-home-image');
                $group->post('/image', AddHomeImageAction::class)->setName('add-home-image');
                $group->delete('/image', DeleteHomeImageAction::class)->setName('delete-home-image');
            });
        });
    });
};
