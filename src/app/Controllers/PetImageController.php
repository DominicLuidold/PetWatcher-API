<?php

namespace PetWatcher\Controllers;

use PetWatcher\Models\Pet;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class PetImageController extends BaseImageController {

    /**
     * Get image of pet based on id
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function get(Request $request, Response $response, array $args) {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $response->withJson(["message" => "Pet not found"], 404);
        }
        if ($pet->image == "") {
            return $response->withJson(["message" => "Image not found"], 404);
        }

        // Read file
        $imagePath = $this->imgUpload['directory'] . $pet->image;
        if (!file_exists($imagePath) || !($image = file_get_contents($imagePath))) {
            $this->logger->addError("Attempt to read image of pet #" . $pet->id . " failed");
            return $response->withJson(["message" => "Internal error"], 500);
        }

        // Response
        $response->write($image);
        return $response->withHeader('Content-Type', 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION));
    }

    /**
     * Add new image of pet. An already existing image will get removed
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function add(Request $request, Response $response, array $args) {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $response->withJson(["message" => "Pet not found"], 404);
        }

        // Input validation
        $uploadedFile = $request->getUploadedFiles()['image'];
        $validation = $this->validator->validate($uploadedFile, [
            'file' => v::image(),
            'size' => v::size(null, $this->imgUpload['maxSize']),
        ], true);
        if ($validation->failed()) {
            return $response->withJSON(["message" => $validation->getErrors()], 400);
        }

        // Upload validation
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->logger->addError("Attempt to add image of pet #" . $pet->id . " failed");
            return $response->withJson(["message" => "Image upload failed"], 500);
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (\Exception $e) {
            $this->logger->addError("Attempt to add image of pet #" . $pet->id . " failed");
            return $response->withJson(["message" => "Image upload failed"], 500);
        }

        // Deletion of old image
        if ($pet->image != "") {
            if (!is_writable($this->imgUpload['directory'] . $pet->image) || !unlink($this->imgUpload['directory'] . $pet->image)) {
                $this->logger->addError("Attempt to delete image of pet #" . $pet->id . " failed");
                return $response->withJson(["message" => "Image update failed"], 500);
            }
        }

        // Database update
        $pet->update([
            'image' => $filename,
        ]);

        // Response
        return $response->withJson(["message" => "Successfully uploaded image"], 201);
    }

    /**
     * Delete image of pet based on id
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function delete(Request $request, Response $response, array $args) {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $response->withJson(["message" => "Pet not found"], 404);
        }
        if ($pet->image == "") {
            return $response->withJson(["message" => "Image not found"], 404);
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $pet->image) || !unlink($this->imgUpload['directory'] . $pet->image)) {
            $this->logger->addError("Attempt to delete image of pet #" . $pet->id . " failed");
            return $response->withJson(["message" => "Image deletion failed"], 500);
        }

        // Database update
        $pet->update([
            'image' => '',
        ]);

        // Response
        $this->logger->addInfo("Deleted image of pet #" . $pet->id . " - '" . $pet->name . "'");
        return $response->withJson(["message" => "Successfully deleted image"], 200);
    }
}
