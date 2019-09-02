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

        // Insert resource-specific URI to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        foreach ($homes as $home) {
            $home->URI = $routeContext->getRouteParser()->relativeUrlFor('view-home', ['id' => $home->id]);
        }

        // Response
        return $this->respondWithJson($homes);
    }
}
