<?php
declare(strict_types=1);

namespace PetWatcher\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class Location extends AbstractRule
{

    /**
     * Validate user input to confirm valid location
     *
     * @param $input
     *
     * @return bool Whether validation has succeeded
     */
    public function validate($input)
    {
        // TODO find appropriate way of validating location
        if ($input == "inside" || $input == "outside" || $input == "vet") {
            return true;
        }
        return false;
    }
}
