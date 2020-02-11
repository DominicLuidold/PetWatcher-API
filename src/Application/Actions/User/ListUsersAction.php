<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\User;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\User;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ListUsersAction extends Action
{
    /**
     * List all users.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $users = User::all();

        // Insert resource-specific URI to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        foreach ($users as $user) {
            $user->URI = $routeContext->getRouteParser()->relativeUrlFor('view-user', ['id' => $user->id]);
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['users' => $users->makeHidden('password')]);
    }
}
