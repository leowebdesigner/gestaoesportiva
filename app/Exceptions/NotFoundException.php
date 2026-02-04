<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public int $httpCode;

    public function __construct(string $message = 'Recurso não encontrado.', int $httpCode = 404)
    {
        parent::__construct($message, 0);
        $this->httpCode = $httpCode;
    }
}
