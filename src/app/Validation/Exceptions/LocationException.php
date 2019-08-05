<?php
declare(strict_types=1);

namespace PetWatcher\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class LocationException extends ValidationException {

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be a valid location'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be a valid location'
        ]
    ];
}
