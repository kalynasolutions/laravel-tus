<?php

namespace KalynaSolutions\Tus\Commands;

use Illuminate\Console\Command;
use KalynaSolutions\Tus\Facades\Tus;

class TusPruneExpiredUploadsCommand extends Command
{
    public $signature = 'tus:prune';

    public $description = 'Remove expired uploads via TUS';

    public function handle(): int
    {
        if ((int) config('tus.upload_expiration') < 1) {
            return self::SUCCESS;
        }

        $now = now()->getTimestamp();

        $files = Tus::storage()->files(config('tus.storage_path'));

        $d = 0;

        foreach ($files as $file) {

            if (Tus::storage()->lastModified($file) + (int) config('tus.upload_expiration') * 60 > $now) {
                continue;
            }

            $d++;

            Tus::storage()->delete($file);

        }

        $this->comment("Deleted expired tus $d files");

        return self::SUCCESS;
    }
}
