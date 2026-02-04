<?php

namespace App\Exceptions;

use Exception;

class ExternalApiException extends Exception
{
    public int $httpCode;

    public function __construct(string $message = 'Erro na API externa.', int $httpCode = 502)
    {
        parent::__construct($message, 0);
        $this->httpCode = $httpCode;
    }
}
