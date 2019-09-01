<?php
declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Domain\Home;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class CreatePetAction extends PetAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        // Input validation
        $validation = $this->validateInput($this->request);
        if ($validation->failed()) {
            return $this->respondWithJson(["message" => $validation->getErrors()], 400);
        }

        // Database query
        $home = Home::find($this->request->getParsedBody()['home_id']);
        if (!$home) {
            return $this->respondWithJson(["message" => "Home not found"], 404);
        }

        // Database insert
        $pet = Pet::create(
            [
                'name' => $this->request->getParsedBody()['name'],
                'dateOfBirth' => $this->request->getParsedBody()['dateOfBirth'],
                'weight' => $this->request->getParsedBody()['weight'],
                'location' => $this->request->getParsedBody()['location'],
            ]
        );
        $home->pets()->save($pet);

        // Response
        $this->logger->info("Created pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(["message" => "Successfully created pet", "id" => $pet->id], 201);
    }
}
