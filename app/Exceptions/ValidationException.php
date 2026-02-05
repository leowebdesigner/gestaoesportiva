<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public int $httpCode;
    public array $errors;

    public function __construct(string $message = 'Invalid data.', array $errors = [], int $httpCode = 422)
    {
        parent::__construct($message, 0);
        $this->httpCode = $httpCode;
        $this->errors = $errors;
    }
}
