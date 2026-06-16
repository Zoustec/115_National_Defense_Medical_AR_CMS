<?php

namespace App\Exceptions;

use Exception;

class RequestException extends Exception
{
    protected $statusCode;

    public function __construct(string $message, int $statusCode = 0)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
