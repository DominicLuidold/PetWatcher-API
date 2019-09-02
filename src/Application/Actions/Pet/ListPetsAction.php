<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ListPetsAction extends Action
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        // Database query
        $pets = Pet::all();

        // Insert resource-specific URI to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        foreach ($pets as $pet) {
            $pet->URI = $routeContext->getRouteParser()->relativeUrlFor('view-pet', ['id' => $pet->id]);
        }

        // Response
        return $this->respondWithJson($pets);
    }
}
