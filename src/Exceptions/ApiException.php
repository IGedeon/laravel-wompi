<?php

namespace IGedeon\WompiLaravel\Exceptions;

class ApiException extends WompiException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly array $responseBody = [],
    ) {
        parent::__construct($message, $statusCode);
    }
}
