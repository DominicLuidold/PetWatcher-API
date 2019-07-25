<?php

namespace PetWatcher\Validation\Rules;

use PetWatcher\Models\Pet;
use Respect\Validation\Rules\AbstractRule;

class Location extends AbstractRule {

    public function validate($input) {
        if ($input == "inside" || $input == "outside" || $input == "vet") {
            return true;
        }
        return false;
    }
}
