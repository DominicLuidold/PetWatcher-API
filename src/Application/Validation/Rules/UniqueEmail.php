<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Rules;

use PetWatcher\Domain\User;
use Respect\Validation\Rules\AbstractRule;

class UniqueEmail extends AbstractRule
{
    /**
     * Validate user input to confirm unique email address.
     *
     * @param string $input
     *
     * @return bool Whether validation has succeeded
     */
    public function validate($input)
    {
        return User::where('email', $input)->doesntExist();
    }
}
