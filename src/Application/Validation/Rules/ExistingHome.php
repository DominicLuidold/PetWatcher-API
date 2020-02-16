<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Rules;

use PetWatcher\Domain\Home;
use Respect\Validation\Rules\AbstractRule;

class ExistingHome extends AbstractRule
{
    /**
     * Validate user input to confirm specified home exists.
     *
     * @param int $input
     *
     * @return bool Whether validation has succeeded and home exists
     */
    public function validate($input)
    {
        return Home::where('id', $input)->exists();
    }
}
