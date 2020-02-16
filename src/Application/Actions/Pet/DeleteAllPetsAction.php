<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use Exception;
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
        $omittedPets = [];
        foreach ($pets as $pet) {
            try {
                $pet->delete();
            } catch (Exception $e) {
                $omittedPets[] = $pet->id;
                $this->logger->error(
                    "Attempted to delete pet #{$pet->id} failed",
                    ['user' => $this->token['user']]
                );
            }
        }

        // Response
        if ($omittedPets) {
            $this->logger->notice(
                'Attempted to delete all pets - some remain untouched',
                ['user' => $this->token['user']]
            );
            return $this->respondWithJson(
                self::FAILURE,
                409,
                ['pets' => $omittedPets],
                'Cannot delete the following pets - an error occurred'
            );
        }
        $this->logger->info('Deleted all pets', ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
