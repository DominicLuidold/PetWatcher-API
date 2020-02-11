<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ExistingUserException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be an existing user'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not an existing user'
        ]
    ];
}