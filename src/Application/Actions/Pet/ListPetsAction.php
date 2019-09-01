<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

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
        foreach ($pets as $pet) {
            $pet->URI = $this->request->getUri()->getPath() . "/" . $pet->id;
        }

        // Response
        return $this->respondWithJson($pets);
    }
}
