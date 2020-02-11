<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Image;

use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteImageAction extends ImageAction
{
    /**
     * Delete an image of a specific model.
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
        if (!$actualModel) {
            return $this->respondWithJson(self::FAILURE, 404, null, "{$modelName} not found");
        }
        if ($actualModel->image == null) {
            return $this->respondWithJson(self::FAILURE, 404, null, 'Image not found');
        }

        // File deletion
        if (
            !is_writable($this->imgUpload['directory'] . $actualModel->image)
            || !unlink($this->imgUpload['directory'] . $actualModel->image)
        ) {
            $this->logger->error(
                "Attempt to delete image of {$modelName} #{$actualModel->id} failed",
                ['user' => $this->token['user']]
            );
            return $this->respondWithJson(self::ERROR, 500, null, 'Image deletion failed');
        }

        // Database update
        $actualModel->update(
            [
                'image' => null,
            ]
        );

        // Response
        $this->logger->info("Deleted image of {$modelName} #{$actualModel->id}", ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, null);
    }
}
