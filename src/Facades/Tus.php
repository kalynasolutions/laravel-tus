<?php

namespace KalynaSolutions\Tus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KalynaSolutions\Tus\Tus
 */
class Tus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \KalynaSolutions\Tus\Tus::class;
    }
}
