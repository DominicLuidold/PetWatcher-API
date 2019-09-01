<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePetAction extends Action
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        // Database query
        $pet = Pet::find($this->args['id']);
        if (!$pet) {
            return $this->respondWithJson(["message" => "Pet not found"], 404);
        }

        // Database delete
        $pet->delete();

        // Response
        $this->logger->info("Deleted pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(["message" => "Successfully deleted pet"]);
    }
}
