<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Rules;

use PetWatcher\Domain\User;
use Respect\Validation\Rules\AbstractRule;

final class UniqueUsername extends AbstractRule
{
    /**
     * Validate user input to confirm unique username.
     *
     * @param string $input
     *
     * @return bool Whether validation has succeeded
     */
    public function validate($input): bool
    {
        return User::where('username', $input)->doesntExist();
    }
}
