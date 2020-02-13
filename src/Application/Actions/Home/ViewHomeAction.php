<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ViewHomeAction extends Action
{
    /**
     * View a specific home.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'Home not found');
        }

        // Insert owner URI to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        $home->owner = $routeContext->getRouteParser()->relativeUrlFor('view-user', ['id' => $home->owner]);

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['home' => $home]);
    }
}
