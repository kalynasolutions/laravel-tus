<?php

namespace KalynaSolutions\Tus\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class FileUploadBeforeCreated
{
    use Dispatchable;

    public function __construct(public Request $request)
    {
        //
    }
}
