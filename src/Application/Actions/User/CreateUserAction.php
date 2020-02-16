<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\User;

use PetWatcher\Domain\User;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    /**
     * Create a new user based on input.
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
        $user = User::create(
            [
                'username' => $this->request->getParsedBody()['username'],
                'email' => $this->request->getParsedBody()['email'],
                'password' => password_hash($this->request->getParsedBody()['password'], PASSWORD_BCRYPT),
                'displayName' => $this->request->getParsedBody()['displayName'],
                'admin' => 0,
            ]
        );

        // Response
        $this->logger->info("Created user #{$user->id} - '{$user->username}'");
        return $this->respondWithJson(self::SUCCESS, 201, ['user' => $user->makeHidden('password')]);
    }
}
