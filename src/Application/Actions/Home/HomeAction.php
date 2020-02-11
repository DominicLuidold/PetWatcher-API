<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Home;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Application\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

abstract class HomeAction extends Action
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
                'name' => v::alnum()->length(1, 255),
                'owner' => v::existingUser(),
            ]
        );
    }
}
