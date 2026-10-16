<?php

namespace App\Exceptions;

use RuntimeException;

class AdminTwoFactorException extends RuntimeException
{
    public static function unavailable(): self
    {
        return new self('Two-factor authentication is unavailable.');
    }
}
