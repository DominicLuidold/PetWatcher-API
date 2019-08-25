<?php
declare(strict_types=1);

namespace PetWatcher\Controllers;

use PetWatcher\Models\Home;
use PetWatcher\Models\Pet;
use PetWatcher\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

class PetController extends BaseController
{

    /**
     * Get information about specific pet
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function info(Request $request, Response $response, array $args): Response
    {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $this->respondWithJson($response, ["message" => "Pet not found"], 404);
        }

        // Response
        return $this->respondWithJson($response, $pet);
    }

    /**
     * Get information about all pets
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function infoAll(Request $request, Response $response): Response
    {
        // Database query
        $pets = Pet::all();

        // Insert resource-specific URI to ease further navigation
        foreach ($pets as $pet) {
            $pet->URI = $request->getUri()->getPath() . "/" . $pet->id;
        }

        // Response
        return $this->respondWithJson($response, $pets);
    }

    /**
     * Create new pet based on input
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        // Input validation
        $validation = $this->validateInput($request);
        if ($validation->failed()) {
            return $this->respondWithJson($response, ["message" => $validation->getErrors()], 400);
        }

        // Database query
        $home = Home::find($request->getParsedBody()['home_id']);
        if (!$home) {
            return $this->respondWithJson($response, ["message" => "Home not found"], 404);
        }

        // Database insert
        $pet = Pet::create(
            [
                'name' => $request->getParsedBody()['name'],
                'dateOfBirth' => $request->getParsedBody()['dateOfBirth'],
                'weight' => $request->getParsedBody()['weight'],
                'location' => $request->getParsedBody()['location'],
            ]
        );
        $home->pets()->save($pet);

        // Response
        $this->logger->info("Created pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully created pet", "id" => $pet->id], 201);
    }

    /**
     * Update pet based on id and input, create new pet if id does not exist
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        // Database query (pet)
        $pet = Pet::find($args['id']);
        if (!$pet) {
            // Create pet if specified id does not exist yet
            return $this->create($request, $response);
        }

        // Input validation
        $validation = $this->validateInput($request);
        if ($validation->failed()) {
            return $this->respondWithJson($response, ["message" => $validation->getErrors()], 400);
        }

        // Database query (home)
        $home = Home::find($request->getParsedBody()['home_id']);
        if (!$home) {
            return $this->respondWithJson($response, ["message" => "Home not found"], 404);
        }

        // Database update
        $pet->update(
            [
                'name' => $request->getParsedBody()['name'],
                'dateOfBirth' => $request->getParsedBody()['dateOfBirth'],
                'weight' => $request->getParsedBody()['weight'],
                'location' => $request->getParsedBody()['location'],
            ]
        );
        $home->pets()->save($pet);

        // Response
        $this->logger->info("Updated pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully updated pet"]);
    }

    /**
     * Delete pet based on id
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        // Database query
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $this->respondWithJson($response, ["message" => "Pet not found"], 404);
        }

        // Database delete
        $pet->delete();

        // Response
        $this->logger->info("Deleted pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully deleted pet"]);
    }

    /**
     * Delete all pets
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteAll(Request $request, Response $response)
    {
        // Database query
        $pets = Pet::all();

        // Database delete
        foreach ($pets as $pet) {
            $pet->delete();
        }

        // Response
        $this->logger->info("Deleted all pets");
        return $this->respondWithJson($response, ["message" => "Successfully deleted all pets"]);
    }

    /**
     * Validate input based on supplied request
     *
     * @param Request $request
     *
     * @return Validator
     */
    private function validateInput(Request $request): Validator
    {
        return $this->validator->validate(
            $request,
            [
                'name' => v::alpha()->length(1, 255),
                'dateOfBirth' => v::unixTimestamp(),
                'weight' => v::numeric(),
                'location' => v::location(),
                'home_id' => v::notEmpty(),
            ]
        );
    }
}
