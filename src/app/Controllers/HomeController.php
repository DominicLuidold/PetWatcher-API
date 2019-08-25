<?php
declare(strict_types=1);

namespace PetWatcher\Controllers;

use PetWatcher\Models\Home;
use PetWatcher\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

class HomeController extends BaseController
{

    /**
     * Get information about specific home
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
        $home = Home::find($args['id']);
        if (!$home) {
            return $this->respondWithJson($response, ["message" => "Home not found"], 404);
        }

        // Response
        return $this->respondWithJson($response, $home);
    }

    /**
     * Get information about all homes
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function infoAll(Request $request, Response $response): Response
    {
        // Database query
        $homes = Home::all();

        // Insert resource-specific URI to ease further navigation
        foreach ($homes as $home) {
            $home->URI = $request->getUri()->getPath() . '/' . $home->id;
        }

        // Response
        return $this->respondWithJson($response, $homes);
    }

    /**
     * Get information about all pets living in this home
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function pets(Request $request, Response $response, array $args): Response
    {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            return $this->respondWithJson($response, ["message" => "Home not found"], 404);
        }
        $pets = $home->pets()->get();

        // Insert resource-specific URI to ease further navigation
        // TODO
        foreach ($pets as $pet) {
            $pet->URI = "";
        }

        // Response
        return $this->respondWithJson($response, $pets);
    }

    /**
     * Create new home based on input
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

        // Database insert
        $home = Home::create(
            [
                'name' => $request->getParsedBody()['name'],
            ]
        );

        // Response
        $this->logger->info("Created home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully created home", "id" => $home->id], 201);
    }

    /**
     * Update home based on id and input, create new home if id does not exist
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        // Database query
        $home = Home::find($args['id']);
        if (!$home) {
            // Create home if specified id does not exist yet
            return $this->create($request, $response);
        }

        // Input validation
        $validation = $this->validateInput($request);
        if ($validation->failed()) {
            return $this->respondWithJson($response, ["message" => $validation->getErrors()], 400);
        }

        // Database update
        $home->update(
            [
                'name' => $request->getParsedBody()['name'],
            ]
        );

        // Response
        $this->logger->info("Updated home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully updated home"]);
    }

    /**
     * Delete home based on id
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
        $home = Home::find($args['id']);
        if (!$home) {
            return $this->respondWithJson($response, ["message" => "Home not found"], 404);
        }

        // Abort deletion if pets still assigned to this home
        if ($home->pets()->get()) {
            return $this->respondWithJson(
                $response,
                [
                    "message" => "Cannot delete home - pets still assigned to this home"
                ],
                409
            );
        }

        // Database delete
        $home->delete();

        // Response
        $this->logger->info("Deleted home #" . $home->id . " - '" . $home->name . "'");
        return $this->respondWithJson($response, ["message" => "Successfully deleted home"]);
    }

    /**
     * Delete all homes
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteAll(Request $request, Response $response): Response
    {
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
            $this->logger->info("Attempted to delete all homes - some remain untouched");
            return $this->respondWithJson(
                $response,
                [
                    "message" => "Cannot delete following homes - pets still assigned",
                    "homes" => $omittedHomes
                ],
                409
            );
        }
        $this->logger->info("Deleted all homes");
        return $this->respondWithJson($response, ["message" => "Successfully deleted all homes"]);
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
                'name' => v::alnum()->length(1, 255),
            ]
        );
    }
}
