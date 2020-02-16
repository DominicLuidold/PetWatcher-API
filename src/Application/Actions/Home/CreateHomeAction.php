<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Domain\Home;
use Psr\Http\Message\ResponseInterface as Response;

class CreateHomeAction extends HomeAction
{
    /**
     * Create a new home based on input.
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
        $home = Home::create(
            [
                'name' => $this->request->getParsedBody()['name'],
                'owner' => $this->request->getParsedBody()['owner'],
            ]
        );

        // Response
        $this->logger->info("Created home #{$home->id} - '{$home->name}'", ['user' => $this->token['user']]);
        return $this->respondWithJson(self::SUCCESS, 201, ['home' => $home]);
    }
}
