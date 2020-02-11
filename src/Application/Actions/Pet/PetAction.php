<?php

declare(strict_types=1);

namespace PetWatcher\Application\Actions\Pet;

use PetWatcher\Application\Actions\Action;
use PetWatcher\Application\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

abstract class PetAction extends Action
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
                'name' => v::alpha()->length(1, 255),
                'dateOfBirth' => v::unixTimestamp(),
                'weight' => v::numeric(),
                'location' => v::alnum()->length(1, 255),
                'home' => v::existingHome(),
            ]
        );
    }
}
