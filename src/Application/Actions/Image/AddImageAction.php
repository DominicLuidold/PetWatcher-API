<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Image;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface as Response;

class AddImageAction extends ImageAction
{
    /**
     * Add new image to a specific model.
     * An already existing image will get replaced.
     *
     * @param Model  $model     Type of model
     * @param string $modelName String representation of model
     *
     * @return Response
     */
    protected function imageAction(Model $model, string $modelName): Response
    {
        // Database query
        $actualModel = $model::find($this->args['id']);
        if (!$model) {
            return $this->respondWithJson(self::FAILURE, 404, null, "{$modelName} not found");
        }

        // Input validation
        $validation = $this->validateUploadedFile($_FILES);
        if ($validation->failed()) {
            return $this->respondWithJson(
                self::FAILURE,
                400,
                $validation->getErrors(),
                'Input does not match requirements'
            );
        }

        // Upload validation
        $uploadedFile = $this->request->getUploadedFiles()['image'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->logger->error(
                "Attempt to add image of {$modelName} #{$actualModel->id} failed",
                ['user' => $this->token['user']]
            );
            return $this->respondWithJson(self::ERROR, 500, null, 'Image upload failed');
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (Exception $e) {
            $this->logger->error(
                "Attempt to add image of {$modelName} #{$actualModel->id} failed",
                ['user' => $this->token['user']]
            );
            return $this->respondWithJson(self::ERROR, 500, null, 'Image upload failed');
        }

        // Deletion of old image
        if ($actualModel->image != null) {
            if (
                !is_writable($this->imgUpload['directory'] . $actualModel->image)
                || !unlink($this->imgUpload['directory'] . $actualModel->image)
            ) {
                $this->logger->error(
                    "Attempt to delete image of {$modelName} #{$actualModel->id} failed",
                    ['user' => $this->token['user']]
                );
                return $this->respondWithJson(self::ERROR, 500, null, 'Image upload failed');
            }
        }

        // Database update
        $actualModel->update(
            [
                'image' => $filename,
            ]
        );

        // Response
        $this->logger->info(
            "Added image to {$modelName} #{$actualModel->id}",
            ['file' => $filename, 'user' => $this->token['user']]
        );
        return $this->respondWithJson(self::SUCCESS, 201, null);
    }
}
