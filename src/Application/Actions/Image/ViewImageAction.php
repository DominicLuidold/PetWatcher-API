<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Image;

use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface as Response;

class ViewImageAction extends ImageAction
{
    /**
     * View the image of a specific model.
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

        // Read file
        $imagePath = $this->imgUpload['directory'] . $actualModel->image;
        if (!file_exists($imagePath) || !($image = file_get_contents($imagePath))) {
            $this->logger->error(
                "Attempt to read image of {$modelName} #{$actualModel->id} failed",
                ['user' => $this->token['user']]
            );
            return $this->respondWithJson(self::ERROR, 500, null, 'Internal error');
        }

        // Response
        $this->response->getBody()->write($image);
        return $this->response->withHeader('Content-Type', 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION));
    }
}
