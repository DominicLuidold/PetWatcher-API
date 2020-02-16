<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ListHomesAction extends Action
{
    /**
     * List all homes.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $homes = Home::all();

        // Insert resource-specific URIs to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        foreach ($homes as $home) {
            if ($home->image != null) {
                $home->image = $routeContext->getRouteParser()->relativeUrlFor('view-home-image', ['id' => $home->id]);
            }
            $home->owner = $routeContext->getRouteParser()->relativeUrlFor('view-user', ['id' => $home->owner]);
            $home['URI'] = $routeContext->getRouteParser()->relativeUrlFor('view-home', ['id' => $home->id]);
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['homes' => $homes]);
    }
}
