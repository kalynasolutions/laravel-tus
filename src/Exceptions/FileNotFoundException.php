<?php

namespace KalynaSolutions\Tus\Exceptions;

use KalynaSolutions\Tus\Facades\Tus;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(
            statusCode: 404,
            headers: Tus::headers()->default()->toArray()
        );
    }
}
