<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\User;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Application\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

abstract class UserAction extends Action
{
    /**
     * Validate input based on supplied request.
     *
     * @param Request $request
     *
     * @return Validator
     */
    protected function validateInput(Request $request): Validator
    {
        return $this->validator->validate(
            $request,
            [
                'email' => v::email()->uniqueEmail(),
                'password' => v::noWhitespace()->length(8, 255),
                'username' => v::alpha()->noWhitespace()->length(1, 255)->uniqueUsername(),
                'displayName' => v::alpha()->length(1, 255),
            ]
        );
    }
}
