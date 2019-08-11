<?php
declare(strict_types=1);

namespace PetWatcher\Controllers;

use PetWatcher\Models\Pet;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PetImageController extends BaseImageController {

    /**
     * Get image of pet based on id
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function get(Request $request, Response $response, array $args): Response {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $this->respondWithJson($response, ["message" => "Pet not found"], 404);
        }
        if ($pet->image == "") {
            return $this->respondWithJson($response, ["message" => "Image not found"], 404);
        }

        // Read file
        $imagePath = $this->imgUpload['directory'] . $pet->image;
        if (!file_exists($imagePath) || !($image = file_get_contents($imagePath))) {
            $this->logger->error("Attempt to read image of pet #" . $pet->id . " failed");
            return $this->respondWithJson($response, ["message" => "Internal error"], 500);
        }

        // Response
        $response->getBody()->write($image);
        return $response->withHeader('Content-Type', 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION));
    }

    /**
     * Add new image of pet. An already existing image will get removed
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function add(Request $request, Response $response, array $args): Response {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $this->respondWithJson($response, ["message" => "Pet not found"], 404);
        }

        // Input validation
        $validation = $this->validateUploadedFile($_FILES);
        if ($validation->failed()) {
            return $this->respondWithJson($response, ["message" => $validation->getErrors()], 400);
        }

        // Upload validation
        $uploadedFile = $request->getUploadedFiles()['image'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->logger->error("Attempt to add image of pet #" . $pet->id . " failed");
            return $this->respondWithJson($response, ["message" => "Image upload failed"], 500);
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (\Exception $e) {
            $this->logger->error("Attempt to add image of pet #" . $pet->id . " failed");
            return $this->respondWithJson($response, ["message" => "Image upload failed"], 500);
        }

        // Deletion of old image
        if ($pet->image != "") {
            if (!is_writable($this->imgUpload['directory'] . $pet->image) || !unlink($this->imgUpload['directory'] . $pet->image)) {
                $this->logger->error("Attempt to delete image of pet #" . $pet->id . " failed");
                return $this->respondWithJson($response, ["message" => "Image upload failed"], 500);
            }
        }

        // Database update
        $pet->update(
            [
                'image' => $filename,
            ]
        );

        // Response
        return $this->respondWithJson($response, ["message" => "Successfully uploaded image"], 201);
    }

    /**
     * Delete image of pet based on id
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * 
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $this->respondWithJson($response, ["message" => "Pet not found"], 404);
        }
        if ($pet->image == "") {
            return $this->respondWithJson($response, ["message" => "Image not found"], 404);
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $pet->image) || !unlink($this->imgUpload['directory'] . $pet->image)) {
            $this->logger->error("Attempt to delete image of pet #" . $pet->id . " failed");
            return $this->respondWithJson($response, ["message" => "Image deletion failed"], 500);
        }

        // Database update
        $pet->update(
            [
                'image' => '',
            ]
        );

        // Response
        $this->logger->info("Deleted image of pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully deleted image"]);
    }
}
