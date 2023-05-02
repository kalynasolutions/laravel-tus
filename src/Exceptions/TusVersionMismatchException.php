<?php

namespace KalynaSolutions\Tus\Exceptions;

use KalynaSolutions\Tus\Facades\Tus;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TusVersionMismatchException extends HttpException
{
    public function __construct()
    {
        parent::__construct(
            statusCode: 412,
            headers: Tus::headers()->forOptions()->toArray()
        );
    }
}
