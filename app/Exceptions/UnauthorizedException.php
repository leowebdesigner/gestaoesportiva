<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public int $httpCode;

    public function __construct(string $message = 'Não autorizado.', int $httpCode = 401)
    {
        parent::__construct($message, 0);
        $this->httpCode = $httpCode;
    }
}
