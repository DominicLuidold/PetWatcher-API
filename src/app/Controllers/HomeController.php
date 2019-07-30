<?php

namespace PetWatcher\Controllers;

use PetWatcher\Models\Home;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends BaseController {

    /**
     * Get information about specific home
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function info(Request $request, Response $response, array $args) {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }

        // Response
        return $response->withJson($home, 200);
    }

    /**
     * Get information about all homes
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function infoAll(Request $request, Response $response) {
        // Database query
        $homes = Home::all();

        // Insert resource-specific URI to ease further navigation
        foreach ($homes as $home) {
            $home->URI = $request->getUri()->getPath() . '/' . $home->id;
        }

        // Response
        return $response->withJson($homes, 200);
    }

    /**
     * Get information about all pets living in this home
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function pets(Request $request, Response $response, array $args) {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }
        $pets = $home->pets()->get();

        // Insert resource-specific URI to ease further navigation
        // TODO
        foreach ($pets as $pet) {
            $pet->URI = "";
        }

        // Response
        return $response->withJson($pets, 200);
    }

    /**
     * Create new home based on input
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function create(Request $request, Response $response) {
        // Input validation
        $validation = $this->validator->validate($request, [
            'name' => v::alnum()->length(1, 255),
        ]);
        if ($validation->failed()) {
            return $response->withJSON(["message" => $validation->getErrors()], 400);
        }

        // Database insert
        $home = Home::create([
            'name' => $request->getParsedBody()['name'],
        ]);

        // Response
        $this->logger->addInfo("Created home #" . $home->id . " - '" . $home->name . "'");
        return $response->withJSON(["message" => "Successfully created home", "id" => $home->id], 201);
    }

    /**
     * Delete home based on id
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

        // Abort deletion if pets still assigned to this home
        if ($home->pets()->get()) {
            return $response->withJson(["message" => "Cannot delete home - pets still assigned to this home"], 409);
        }

        // Database delete
        $home->delete();

        // Response
        $this->logger->addInfo("Deleted home #" . $home->id . " - '" . $home->name . "'");
        return $response->withJson(["message" => "Successfully deleted home"], 200);
    }

    /**
     * Delete all homes
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function deleteAll(Request $request, Response $response) {
        // Database query
        $homes = Home::all();

        // Database delete
        $omittedHomes = [];
        foreach ($homes as $home) {
            // Skip deletion if pets still assigned to this home
            if ($home->pets()->get()->isEmpty()) {
                $home->delete();
            } else {
                $omittedHomes[] = $home->id;
            }
        }

        // Response
        if ($omittedHomes) {
            $this->logger->addInfo("Attempted to delete all homes - some remain untouched");
            return $response->withJson(["message" => "Cannot delete following homes - pets still assigned",
                "homes" => $omittedHomes], 409);
        }
        $this->logger->addInfo("Deleted all homes");
        return $response->withJson(["message" => "Successfully deleted all homes"], 200);
    }
}
