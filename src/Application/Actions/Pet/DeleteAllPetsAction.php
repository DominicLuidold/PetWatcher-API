<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteAllPetsAction extends Action
{
    /**
     * Delete all pets.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $pets = Pet::all();

        // Database delete
        foreach ($pets as $pet) {
            $pet->delete();
        }

        // Response
        $this->logger->info("Deleted all pets");
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
