<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UniqueUsernameException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be unique'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be unique'
        ]
    ];
}
