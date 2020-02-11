<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ExistingHomeException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be an existing home'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not an existing home'
        ]
    ];
}
