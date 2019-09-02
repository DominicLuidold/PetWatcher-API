<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class ViewHomeImageAction extends ImageAction
{
    /**
     * View the image of a specific home.
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
        if ($home->image == "") {
            return $this->respondWithJson(["message" => "Image not found"], 404);
        }

        // Read file
        $imagePath = $this->imgUpload['directory'] . $home->image;
        if (!file_exists($imagePath) || !($image = file_get_contents($imagePath))) {
            $this->logger->error("Attempt to read image of home #" . $home->id . " failed");
            return $this->respondWithJson(["message" => "Internal error"], 500);
        }

        // Response
        $this->response->getBody()->write($image);
        return $this->response->withHeader('Content-Type', 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION));
    }
}
