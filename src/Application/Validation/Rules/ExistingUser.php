<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Rules;

use PetWatcher\Domain\User;
use Respect\Validation\Rules\AbstractRule;

class ExistingUser extends AbstractRule
{
    /**
     * Validate user input to confirm specified user exists.
     *
     * @param $input
     *
     * @return bool Whether validation has succeeded and user exists
     */
    public function validate($input)
    {
        return User::where('id', $input)->exists();
    }
}
