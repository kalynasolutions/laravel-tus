<?php

namespace KalynaSolutions\Tus\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use KalynaSolutions\Tus\Exceptions\FileSizeLimitException;
use KalynaSolutions\Tus\Exceptions\TusVersionMismatchException;
use KalynaSolutions\Tus\Facades\Tus;
use Symfony\Component\HttpFoundation\Response;

class ValidateFileSizeMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     *
     * @throws TusVersionMismatchException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ((int) config('tus.file_size_limit') > 0) {

            if ($request->hasHeader('upload-length') && ! Tus::isInMaxFileSize((int) $request->header('upload-length'))) {
                throw new FileSizeLimitException;
            }

            if ($request->hasHeader('content-length') && ! Tus::isInMaxFileSize((int) $request->header('upload-length'))) {
                throw new FileSizeLimitException;
            }

        }

        return $next($request);
    }
}
