<?php

namespace App\Exceptions;

use Exception;

final class RateLimitExceededException extends Exception
{
    public function __construct(
        public readonly int $retryAfterSeconds = 60,
        string $message = 'Rate limit exceeded'
    ) {
        parent::__construct($message);
    }
}
