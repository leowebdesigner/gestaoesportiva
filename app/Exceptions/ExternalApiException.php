<?php

namespace App\Exceptions;

use Exception;

class ExternalApiException extends Exception
{
    public int $httpCode;

    public function __construct(string $message = 'Error external api.', int $httpCode = 502)
    {
        parent::__construct($message, 0);
        $this->httpCode = $httpCode;
    }
}
