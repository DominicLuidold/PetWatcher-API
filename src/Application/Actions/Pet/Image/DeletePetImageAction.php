<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePetImageAction extends ImageAction
{
    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        // Database query
        $pet = Pet::find($this->args['id']);
        if (!$pet) {
            return $this->respondWithJson(["message" => "Pet not found"], 404);
        }
        if ($pet->image == "") {
            return $this->respondWithJson(["message" => "Image not found"], 404);
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $pet->image)
            || !unlink($this->imgUpload['directory'] . $pet->image)) {
            $this->logger->error("Attempt to delete image of pet #" . $pet->id . " failed");
            return $this->respondWithJson(["message" => "Image deletion failed"], 500);
        }

        // Database update
        $pet->update(
            [
                'image' => '',
            ]
        );

        // Response
        $this->logger->info("Deleted image of pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(["message" => "Successfully deleted image"]);
    }
}
