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
     * List all users, if sufficient permissions are given.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Restrict viewing list of users to admins
        if (!$this->token['admin']) {
            return $this->respondWithJson(self::FAILURE, 401, null, 'Insufficient permissions');
        }

        // Database query
        $users = User::all();

        // Insert resource-specific URIs to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        foreach ($users as $user) {
            if ($user->image != null) {
                $user->image = $routeContext->getRouteParser()->relativeUrlFor('view-user-image', ['id' => $user->id]);
            }
            $user['URI'] = $routeContext->getRouteParser()->relativeUrlFor('view-user', ['id' => $user->id]);
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['users' => $users->makeHidden('password')]);
    }
}
