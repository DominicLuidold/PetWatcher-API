<?php

namespace PetWatcher\Controllers;

use PetWatcher\Models\Home;
use PetWatcher\Models\Pet;
use PetWatcher\Validation\Validator;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class PetController extends BaseController {

    /**
     * Get information about specific pet
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function info(Request $request, Response $response, array $args) {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $response->withJson(["message" => "Pet not found"], 404);
        }

        // Response
        return $response->withJson($pet, 200);
    }

    /**
     * Get information about all pets
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function infoAll(Request $request, Response $response) {
        // Database query
        $pets = Pet::all();

        // Insert resource-specific URI to ease further navigation
        foreach ($pets as $pet) {
            $pet->URI = $request->getUri()->getPath() . "/" . $pet->id;
        }

        // Response
        return $response->withJson($pets, 200);
    }

    /**
     * Create new pet based on input
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function create(Request $request, Response $response) {
        // Input validation
        $validation = $this->validateInput($request);
        if ($validation->failed()) {
            return $response->withJSON(["message" => $validation->getErrors()], 400);
        }

        // Database query
        $home = Home::find($request->getParsedBody()['home_id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }

        // Database insert
        $pet = Pet::create([
            'name' => $request->getParsedBody()['name'],
            'dateOfBirth' => $request->getParsedBody()['dateOfBirth'],
            'weight' => $request->getParsedBody()['weight'],
            'location' => $request->getParsedBody()['location'],
        ]);
        $home->pets()->save($pet);

        // Response
        $this->logger->addInfo("Created pet #" . $pet->id . " - '" . $pet->name . "'");
        return $response->withJSON(["message" => "Successfully created pet", "id" => $pet->id], 201);
    }

    /**
     * Update pet based on id and input, create new pet if id does not exist
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function update(Request $request, Response $response, array $args) {
        // Database query (pet)
        $pet = Pet::find($args['id']);
        if (!$pet) {
            // Create pet if specified id does not exist yet
            return $this->create($request, $response);
        }

        // Input validation
        $validation = $this->validateInput($request);
        if ($validation->failed()) {
            return $response->withJSON(["message" => $validation->getErrors()], 400);
        }

        // Database query (home)
        $home = Home::find($request->getParsedBody()['home_id']);
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }

        // Database update
        $pet->update([
            'name' => $request->getParsedBody()['name'],
            'dateOfBirth' => $request->getParsedBody()['dateOfBirth'],
            'weight' => $request->getParsedBody()['weight'],
            'location' => $request->getParsedBody()['location'],
        ]);
        $home->pets()->save($pet);

        // Response
        $this->logger->addInfo("Updated pet #" . $pet->id . " - '" . $pet->name . "'");
        return $response->withJson(["message" => "Successfully updated pet"], 200);
    }

    /**
     * Delete pet based on id
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

        // Database delete
        $pet->delete();

        // Response
        $this->logger->addInfo("Deleted pet #" . $pet->id . " - '" . $pet->name . "'");
        return $response->withJson(["message" => "Successfully deleted pet"], 200);
    }

    /**
     * Delete all pets
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function deleteAll(Request $request, Response $response) {
        // Database query
        $pets = Pet::all();

        // Database delete
        foreach ($pets as $pet) {
            $pet->delete();
        }

        // Response
        $this->logger->addInfo("Deleted all pets");
        return $response->withJson(["message" => "Successfully deleted all pets"], 200);
    }

    /**
     * Validate input based on supplied request
     *
     * @param \Slim\Http\Request $request
     * @return \PetWatcher\Validation\Validator
     */
    private function validateInput(Request $request): Validator {
        return $this->validator->validate($request, [
            'name' => v::alpha()->length(1, 255),
            'dateOfBirth' => v::unixTimestamp(),
            'weight' => v::numeric(),
            'location' => v::location(),
        ]);
    }
}
