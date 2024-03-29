<?php

declare(strict_types=1);

use PetWatcher\Application\Actions\Home\CreateHomeAction;
use PetWatcher\Application\Actions\Home\DeleteHomeAction;
use PetWatcher\Application\Actions\Home\DeleteAllHomesAction;
use PetWatcher\Application\Actions\Image\AddImageAction;
use PetWatcher\Application\Actions\Image\DeleteImageAction;
use PetWatcher\Application\Actions\Image\ViewImageAction;
use PetWatcher\Application\Actions\Home\ListHomePetsAction;
use PetWatcher\Application\Actions\Home\ListHomesAction;
use PetWatcher\Application\Actions\Home\UpdateHomeAction;
use PetWatcher\Application\Actions\Home\ViewHomeAction;
use PetWatcher\Application\Actions\Pet\CreatePetAction;
use PetWatcher\Application\Actions\Pet\DeletePetAction;
use PetWatcher\Application\Actions\Pet\DeleteAllPetsAction;
use PetWatcher\Application\Actions\Pet\ListPetsAction;
use PetWatcher\Application\Actions\Pet\UpdatePetAction;
use PetWatcher\Application\Actions\Pet\ViewPetAction;
use PetWatcher\Application\Actions\Token\GenerateTokenAction;
use PetWatcher\Application\Actions\Token\RevokeTokenAction;
use PetWatcher\Application\Actions\User\CreateUserAction;
use PetWatcher\Application\Actions\User\DeleteAllUsersAction;
use PetWatcher\Application\Actions\User\DeleteUserAction;
use PetWatcher\Application\Actions\User\ListUsersAction;
use PetWatcher\Application\Actions\User\ViewUserAction;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    // API v1
    $app->group('/v1', function (Group $group) {
        // Users
        $group->get('/users', ListUsersAction::class)->setName('list-users');
        $group->post('/users', CreateUserAction::class)->setName('create-user');
        $group->delete('/users', DeleteAllUsersAction::class)->setName('delete-users');
        $group->get('/users/{id}', ViewUserAction::class)->setName('view-user');
        $group->delete('/users/{id}', DeleteUserAction::class)->setName('delete-user');

        // User Images
        $group->group('/users/{id}', function (Group $group) {
            $group->get('/image', ViewImageAction::class)->setName('view-user-image');
            $group->post('/image', AddImageAction::class)->setName('add-user-image');
            $group->delete('/image', DeleteImageAction::class)->setName('delete-user-image');
        });

        // Token
        $group->post('/token', GenerateTokenAction::class)->setName('generate-token');
        $group->post('/token/revoke', RevokeTokenAction::class)->setName('revoke-token');

        // Pets
        $group->get('/pets', ListPetsAction::class)->setName('list-pets');
        $group->post('/pets', CreatePetAction::class)->setName('create-pet');
        $group->delete('/pets', DeleteAllPetsAction::class)->setName('delete-pets');
        $group->get('/pets/{id}', ViewPetAction::class)->setName('view-pet');
        $group->put('/pets/{id}', UpdatePetAction::class)->setName('update-pet');
        $group->delete('/pets/{id}', DeletePetAction::class)->setName('delete-pet');

        // Pet Images
        $group->group('/pets/{id}', function (Group $group) {
            $group->get('/image', ViewImageAction::class)->setName('view-pet-image');
            $group->post('/image', AddImageAction::class)->setName('add-pet-image');
            $group->delete('/image', DeleteImageAction::class)->setName('delete-pet-image');
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
            $group->get('/image', ViewImageAction::class)->setName('view-home-image');
            $group->post('/image', AddImageAction::class)->setName('add-home-image');
            $group->delete('/image', DeleteImageAction::class)->setName('delete-home-image');
        });
    });
};
