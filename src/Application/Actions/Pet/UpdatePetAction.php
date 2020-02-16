<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Domain\Home;
use PetWatcher\Domain\Pet;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

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
        if ($pet == null) {
            // Create pet if specified id does not exist yet
            return $this->response->withHeader(
                'Location',
                RouteContext::fromRequest($this->request)->getRouteParser()->relativeUrlFor('create-pet')
            )->withStatus(307);
        }

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

        // Database update
        $pet->update(
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
        $this->logger->info("Updated pet #{$pet->id} - '{$pet->name}'", ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 200, ['pet' => $pet]);
    }
}
