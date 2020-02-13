<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

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
        if (!$pet) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'Pet not found');
        }

        // Response
        return $this->respondWithJson(self::SUCCESS, 200, ['pet' => $pet]);
    }
}
