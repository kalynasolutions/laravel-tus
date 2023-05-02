<?php

namespace KalynaSolutions\Tus\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use KalynaSolutions\Tus\Facades\Tus;

class TusClearExpiredUploadsCommand extends Command
{
    public $signature = 'tus:clear-expired';

    public $description = 'Remove expired uploads via TUS';

    public function handle(): int
    {
        if ((int) config('tus.upload_expiration') < 1) {
            return self::SUCCESS;
        }

        Tus::storage()->files(config('tus.storage_path'));

        $this->comment('All done');

        return self::SUCCESS;
    }
}
