<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home\Image;

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
            return $this->respondWithJson(["message" => "Home not found"], 404);
        }

        // Input validation
        $validation = $this->validateUploadedFile($_FILES);
        if ($validation->failed()) {
            return $this->respondWithJson(["message" => $validation->getErrors()], 400);
        }

        // Upload validation
        $uploadedFile = $this->request->getUploadedFiles()['image'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->logger->error("Attempt to add image of home #" . $home->id . " failed");
            return $this->respondWithJson(["message" => "Image upload failed"], 500);
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (\Exception $e) {
            $this->logger->error("Attempt to add image of home #" . $home->id . " failed");
            return $this->respondWithJson(["message" => "Image upload failed"], 500);
        }

        // Deletion of old image
        if ($home->image != "") {
            if (!is_writable($this->imgUpload['directory'] . $home->image)
                || !unlink($this->imgUpload['directory'] . $home->image)) {
                $this->logger->error("Attempt to delete image of home #" . $home->id . " failed");
                return $this->respondWithJson(["message" => "Image upload failed"], 500);
            }
        }

        // Database update
        $home->update(
            [
                'image' => $filename,
            ]
        );

        // Response
        return $this->respondWithJson(["message" => "Successfully uploaded image"], 201);
    }
}
