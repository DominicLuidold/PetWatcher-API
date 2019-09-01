<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class AddPetImageAction extends ImageAction
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

        // Input validation
        $validation = $this->validateUploadedFile($_FILES);
        if ($validation->failed()) {
            return $this->respondWithJson(["message" => $validation->getErrors()], 400);
        }

        // Upload validation
        $uploadedFile = $this->request->getUploadedFiles()['image'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->logger->error("Attempt to add image of pet #" . $pet->id . " failed");
            return $this->respondWithJson(["message" => "Image upload failed"], 500);
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (\Exception $e) {
            $this->logger->error("Attempt to add image of pet #" . $pet->id . " failed");
            return $this->respondWithJson(["message" => "Image upload failed"], 500);
        }

        // Deletion of old image
        if ($pet->image != "") {
            if (!is_writable($this->imgUpload['directory'] . $pet->image)
                || !unlink($this->imgUpload['directory'] . $pet->image)) {
                $this->logger->error("Attempt to delete image of pet #" . $pet->id . " failed");
                return $this->respondWithJson(["message" => "Image upload failed"], 500);
            }
        }

        // Database update
        $pet->update(
            [
                'image' => $filename,
            ]
        );

        // Response
        return $this->respondWithJson(["message" => "Successfully uploaded image"], 201);
    }
}
