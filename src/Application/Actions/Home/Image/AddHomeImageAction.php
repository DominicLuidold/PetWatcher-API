<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home\Image;

use Exception;
use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class AddHomeImageAction extends ImageAction
{
    /**
     * Add a new image to a specific home.
     * An already existing image will get replaced.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $home = Home::find($this->args['id']);
        if (!$home) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Home not found");
        }

        // Input validation
        $validation = $this->validateUploadedFile($_FILES);
        if ($validation->failed()) {
            if ($validation->failed()) {
                return $this->respondWithJson(
                    self::FAILURE,
                    400,
                    $validation->getErrors(),
                    "Input does not match requirements"
                );
            }
        }

        // Upload validation
        $uploadedFile = $this->request->getUploadedFiles()['image'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->logger->error("Attempt to add image of home #" . $home->id . " failed");
            return $this->respondWithJson(self::ERROR, 500, null, "Image upload failed");
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (Exception $e) {
            $this->logger->error("Attempt to add image of home #" . $home->id . " failed");
            return $this->respondWithJson(self::ERROR, 500, null, "Image upload failed");
        }

        // Deletion of old image
        if ($home->image != null) {
            if (
                !is_writable($this->imgUpload['directory'] . $home->image)
                || !unlink($this->imgUpload['directory'] . $home->image)
            ) {
                $this->logger->error("Attempt to delete image of home #" . $home->id . " failed");
                return $this->respondWithJson(self::ERROR, 500, null, "Image upload failed");
            }
        }

        // Database update
        $home->update(
            [
                'image' => $filename,
            ]
        );

        // Response
        return $this->respondWithJson(self::SUCCESS, 201, null);
    }
}
