<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ListHomePetsAction extends Action
{
    /**
     * List all pets living in specific home.
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
        $pets = $home->pets()->get();

        // Insert resource-specific URI to ease further navigation
        $routeContext = RouteContext::fromRequest($this->request);
        foreach ($pets as $pet) {
            $pet->URI = $routeContext->getRouteParser()->relativeUrlFor('view-pet', ['id' => $pet->id]);
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['pets' => $pets]);
    }
}
