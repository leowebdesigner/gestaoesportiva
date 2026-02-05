<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public int $httpCode;

    public function __construct(string $message = 'Unauthorized.', int $httpCode = 401)
    {
        parent::__construct($message, 0);
        $this->httpCode = $httpCode;
    }
}
