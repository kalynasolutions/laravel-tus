<?php

namespace KalynaSolutions\Tus\Exceptions;

use KalynaSolutions\Tus\Facades\Tus;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChecksumMismatchException extends HttpException
{
    public function __construct()
    {
        parent::__construct(
            statusCode: 460,
            headers: Tus::headers()->default()->toArray()
        );
    }
}
