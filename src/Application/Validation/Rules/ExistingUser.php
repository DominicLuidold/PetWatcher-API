<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Rules;

use PetWatcher\Domain\User;
use Respect\Validation\Rules\AbstractRule;

final class ExistingUser extends AbstractRule
{
    /**
     * Validate user input to confirm specified user exists.
     *
     * @param int $input
     *
     * @return bool Whether validation has succeeded and user exists
     */
    public function validate($input): bool
    {
        return User::where('id', $input)->exists();
    }
}
