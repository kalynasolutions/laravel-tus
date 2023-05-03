<?php

namespace KalynaSolutions\Tus\Exceptions;

use KalynaSolutions\Tus\Facades\Tus;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileAppendException extends HttpException
{
    public function __construct(int $statusCode = 500, string $message = '', array $headers = [])
    {
        parent::__construct(
            statusCode: $statusCode,
            message: $message,
            headers: empty($headers) ? Tus::headers()->default()->toArray() : $headers
        );
    }
}
