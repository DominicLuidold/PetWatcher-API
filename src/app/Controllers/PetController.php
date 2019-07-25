<?php

namespace PetWatcher\Controllers;

use PetWatcher\Models\Home;
use PetWatcher\Models\Pet;
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
        $pet = Pet::find($args['id']);
        if (!$pet) {
            return $response->withJson(["message" => "Pet not found"], 404);
        }

        return $response->withJson($pet, 200);
    }

    /**
     * Get information about all pets
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @return \Slim\Http\Response
     */
    public function all(Request $request, Response $response) {
        $query = Pet::all();

        // Insert resource-specific URI to ease further navigation
        $pets = [];
        foreach ($query as $pet) {
            $pet['URI'] = $request->getUri()->getPath() . '/' . $pet['id'];
            $pets[] = $pet;
        }

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
        $validation = $this->validator->validate($request, [
            'name' => v::alpha()->length(1, 255),
            'dateOfBirth' => v::unixTimestamp(),
            'location' => v::location(),
            'home_id' => v::numeric(),
        ]);
        if ($validation->failed()) {
            return $response->withJSON(["message" => $validation->getErrors()], 400);
        }
        $home = Home::find($request->getParam('home_id'));
        if (!$home) {
            return $response->withJson(["message" => "Home not found"], 404);
        }

        // Database insert
        $pet = Pet::create([
            'name' => $request->getParam('name'),
            'dateOfBirth' => $request->getParam('dateOfBirth'),
            'location' => $request->getParam('location'),
        ]);
        $home->pets()->save($pet);

        $this->logger->addInfo("Created pet '" . $request->getQueryParams()['name'] . "'");
        return $response->withJSON(["id" => $pet->id], 201);
    }
}
