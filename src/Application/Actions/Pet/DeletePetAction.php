<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use Exception;
use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePetAction extends Action
{
    /**
     * Delete a specific pet.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $pet = Pet::find($this->args['id']);
        if (!$pet) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Pet not found");
        }

        // Database delete
        try {
            $pet->delete();
        } catch (Exception $e) {
            $this->logger->error("Attempt to delete pet #" . $pet->id . " failed");
            return $this->respondWithJson(self::ERROR, 500, null, "Pet deletion failed");
        }

        // Response
        $this->logger->info("Deleted pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
