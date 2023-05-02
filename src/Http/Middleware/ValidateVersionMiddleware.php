<?php

namespace KalynaSolutions\Tus\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use KalynaSolutions\Tus\Exceptions\TusVersionMismatchException;
use KalynaSolutions\Tus\Facades\Tus;
use Symfony\Component\HttpFoundation\Response;

class ValidateVersionMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     *
     * @throws TusVersionMismatchException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('tus-resumable') !== Tus::version()) {
            throw new TusVersionMismatchException;
        }

        return $next($request);
    }
}
