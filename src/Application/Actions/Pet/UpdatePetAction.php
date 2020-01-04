<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Domain\Home;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class UpdatePetAction extends PetAction
{
    /**
     * Update a specific pet or create a new one, if pet does not exist.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Database query (pet)
        $pet = Pet::find($this->args['id']);
        if (!$pet) {
            // Create pet if specified id does not exist yet
            return $this->response->withHeader('Location', '/api/v1/pets')->withStatus(307);
        }

        // Input validation
        $validation = $this->validateInput($this->request);
        if ($validation->failed()) {
            return $this->respondWithJson(
                self::FAILURE,
                400,
                $validation->getErrors(),
                "Input does not match requirements"
            );
        }

        // Database query (home)
        $home = Home::find($this->request->getParsedBody()['home_id']);
        if (!$home) {
            return $this->respondWithJson(self::FAILURE, 404, null, "Home not found");
        }

        // Database update
        $pet->update(
            [
                'name' => $this->request->getParsedBody()['name'],
                'dateOfBirth' => $this->request->getParsedBody()['dateOfBirth'],
                'weight' => $this->request->getParsedBody()['weight'],
                'location' => $this->request->getParsedBody()['location'],
            ]
        );
        $home->pets()->save($pet);

        // Response
        $this->logger->info("Updated pet #" . $pet->id . " - '" . $pet->name . "'");
        return $this->respondWithJson(self::SUCCESS, 200, ['pet' => $pet]);
    }
}
