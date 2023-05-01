<?php

namespace KalynaSolutions\Tus\Commands;

use Illuminate\Console\Command;

class TusCommand extends Command
{
    public $signature = 'laravel-tus';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
