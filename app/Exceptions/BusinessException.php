<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public int $httpCode;

    public function __construct(string $message, string $code = 'BUSINESS_ERROR', int $httpCode = 400)
    {
        parent::__construct($message, 0);
        $this->code = $code;
        $this->httpCode = $httpCode;
    }
}
