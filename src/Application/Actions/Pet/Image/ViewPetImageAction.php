<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet\Image;

use PetWatcher\Application\Actions\ImageAction;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class ViewPetImageAction extends ImageAction
{
    /**
     * View the image of a specific pet.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query
        $pet = Pet::find($this->args['id']);
        if (!$pet) {
            return $this->respondWithJson(["message" => "Pet not found"], 404);
        }
        if ($pet->image == "") {
            return $this->respondWithJson(["message" => "Image not found"], 404);
        }

        // Read file
        $imagePath = $this->imgUpload['directory'] . $pet->image;
        if (!file_exists($imagePath) || !($image = file_get_contents($imagePath))) {
            $this->logger->error("Attempt to read image of pet #" . $pet->id . " failed");
            return $this->respondWithJson(["message" => "Internal error"], 500);
        }

        // Response
        $this->response->getBody()->write($image);
        return $this->response->withHeader('Content-Type', 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION));
    }
}
