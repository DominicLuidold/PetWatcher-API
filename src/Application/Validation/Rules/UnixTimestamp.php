<?php
declare(strict_types=1);

namespace PetWatcher\Application\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class UnixTimestamp extends AbstractRule
{

    /**
     * Validate user input to confirm valid Unix timestamp
     *
     * @param $input
     *
     * @return bool Whether validation has succeeded
     */
    public function validate($input)
    {
        return ((string)(int)$input === $input)
            && ($input <= PHP_INT_MAX)
            && ($input >= ~PHP_INT_MAX);
    }
}
