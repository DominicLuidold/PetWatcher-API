<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePetImageAction extends ImageAction
{
    /**
     * Delete an image of a specific pet.
     *
     * @return Response
     */
    public function action(): Response
    {
        // Database query
        $pet = Pet::find($this->args['id']);
        if (!$pet) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Pet not found");
        }
        if ($pet->image == null) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Image not found");
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $pet->image)
            || !unlink($this->imgUpload['directory'] . $pet->image)) {
            $this->logger->error("Attempt to delete image of pet #" . $pet->id . " failed");
            return $this->respondWithJson(self::ERROR, 500, null, "Image deletion failed");
        }

        // Database update
        $pet->update(
            [
                'image' => null,
            ]
        );

        // Response
        $this->logger->info("Deleted image of pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
