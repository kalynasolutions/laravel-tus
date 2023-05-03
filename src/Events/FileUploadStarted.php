<?php

namespace KalynaSolutions\Tus\Events;

use Illuminate\Foundation\Events\Dispatchable;
use KalynaSolutions\Tus\Helpers\TusFile;

class FileUploadStarted
{
    use Dispatchable;

    public function __construct(public TusFile $tusFile)
    {
        //
    }
}
