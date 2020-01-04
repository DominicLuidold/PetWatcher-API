<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UnixTimestampException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be a valid Unix timestamp'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be a valid Unix timestamp'
        ]
    ];
}
