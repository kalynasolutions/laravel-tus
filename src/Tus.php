<?php

namespace KalynaSolutions\Tus;

use Illuminate\Support\Facades\Date;
use KalynaSolutions\Tus\Helpers\TusHeaderBuilder;

class Tus
{
    protected const VERSION = '1.0.0';

    public function version(): string
    {
        return static::VERSION;
    }

    public function headers(): TusHeaderBuilder
    {
        return new TusHeaderBuilder(static::VERSION);
    }

    public function parseMetadata(string $rawMetadata): array
    {
        return str($rawMetadata)
            ->explode(',')
            ->mapWithKeys(function (string $data) {
                $data = explode(' ', $data);

                return [$data[0] => $data[1]];
            })
            ->toArray();
    }

    public function isValidChecksum(string $algo, string $hash, string $payload): bool
    {
        if ($hash === hash($algo, $payload)) {
            return true;
        }

        return false;
    }

    public function isUploadExpired(int $lastModified): bool
    {
        return Date::createFromTimestamp($lastModified)->addMinutes((int) config('tus.upload_expiration'))->isPast();
    }

    public function isInMaxFileSize(int $size): bool
    {
        $limit = config('tus.file_size_limit') ? (int) config('tus.file_size_limit') : (int) ini_get('post_max_size');

        return $limit > $size;
    }

    public function extensionIsActive(string $extension): bool
    {
        return in_array($extension, (array) config('tus.extensions'));
    }
}
