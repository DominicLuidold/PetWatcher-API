<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class ViewPetAction extends Action
{
    /**
     * View a specific pet.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $pet = Pet::find($this->args['id']);
        if ($pet == null) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'Pet not found');
        }

        // Insert resource-specific URIs to ease further navigation
        if ($pet->image != null) {
            $pet->image = RouteContext::fromRequest($this->request)->getRouteParser()->relativeUrlFor(
                'view-pet-image',
                ['id' => $pet->id]
            );
        }
        $pet->home = RouteContext::fromRequest($this->request)->getRouteParser()->relativeUrlFor(
            'view-home',
            ['id' => $pet->home]
        );

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['pet' => $pet]);
    }
}
