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
            return $this->respondWithJson(["message" => "Pet not found"], 404);
        }

        // Database delete
        try {
            $pet->delete();
        } catch (Exception $e) {
            $this->logger->error("Attempt to delete pet #" . $pet->id . " failed");
            return $this->respondWithJson(["message" => "Pet deletion failed"], 500);
        }

        // Response
        $this->logger->info("Deleted pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(["message" => "Successfully deleted pet"]);
    }
}
