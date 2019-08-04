<?php

namespace PetWatcher\Controllers;

use PetWatcher\Models\Home;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeImageController extends BaseImageController {

    /**
     * Get image of home based on id
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function get(Request $request, Response $response, array $args) {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }
        if ($home->image == "") {
            return $response->withJson(["message" => "Image not found"], 404);
        }

        // Read file
        $imagePath = $this->imgUpload['directory'] . $home->image;
        if (!file_exists($imagePath) || !($image = file_get_contents($imagePath))) {
            $this->logger->addError("Attempt to read image of home #" . $home->id . " failed");
            return $response->withJson(["message" => "Internal error"], 500);
        }

        // Response
        $response->write($image);
        return $response->withHeader('Content-Type', 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION));
    }

    /**
     * Add new image of home. An already existing image will get removed
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function add(Request $request, Response $response, array $args) {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
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
            $this->logger->addError("Attempt to add image of home #" . $home->id . " failed");
            return $response->withJson(["message" => "Image upload failed"], 500);
        }

        // Move file
        try {
            $filename = $this->moveUploadedFile($this->imgUpload['directory'], $uploadedFile);
        } catch (\Exception $e) {
            $this->logger->addError("Attempt to add image of home #" . $home->id . " failed");
            return $response->withJson(["message" => "Image upload failed"], 500);
        }

        // Deletion of old image
        if ($home->image != "") {
            if (!is_writable($this->imgUpload['directory'] . $home->image) || !unlink($this->imgUpload['directory'] . $home->image)) {
                $this->logger->addError("Attempt to delete image of home #" . $home->id . " failed");
                return $response->withJson(["message" => "Image update failed"], 500);
            }
        }

        // Database update
        $home->update([
            'image' => $filename,
        ]);

        // Response
        return $response->withJson(["message" => "Successfully uploaded image"], 201);
    }

    /**
     * Delete image of home based on id
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function delete(Request $request, Response $response, array $args) {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }
        if ($home->image == "") {
            return $response->withJson(["message" => "Image not found"], 404);
        }

        // File deletion
        if (!is_writable($this->imgUpload['directory'] . $home->image) || !unlink($this->imgUpload['directory'] . $home->image)) {
            $this->logger->addError("Attempt to delete image of home #" . $home->id . " failed");
            return $response->withJson(["message" => "Image deletion failed"], 500);
        }

        // Database update
        $home->update([
            'image' => '',
        ]);

        // Response
        $this->logger->addInfo("Deleted image of home #" . $home->id . " - '" . $home->name . "'");
        return $response->withJson(["message" => "Successfully deleted image"], 200);
    }
}
