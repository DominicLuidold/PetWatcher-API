<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Domain\Home;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;

class CreatePetAction extends PetAction
{
    /**
     * Create a new pet based on input.
     *
     * @return Response
     */
    protected function action(): Response
    {
        // Input validation
        $validation = $this->validateInput($this->request);
        if ($validation->failed()) {
            return $this->respondWithJson(
                self::FAILURE,
                400,
                $validation->getErrors(),
                'Input does not match requirements'
            );
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
        $home = Home::find($this->request->getParsedBody()['home']);
        $home->pets()->save($pet);

        // Response
        $this->logger->info("Created pet #{$pet->id} - '{$pet->name}'");
        return $this->respondWithJson(self::SUCCESS, 201, ['pet' => $pet]);
    }
}
