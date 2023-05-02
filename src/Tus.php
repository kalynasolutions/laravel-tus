<?php

namespace KalynaSolutions\Tus;

use _PHPStan_532094bc1\Nette\Neon\Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use KalynaSolutions\Tus\Helpers\TusHeaderBuilder;
use KalynaSolutions\Tus\Helpers\TusUploadMetadataManager;

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

    public function metadata(): TusUploadMetadataManager
    {
        return new TusUploadMetadataManager;
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

    public function maxFileSize(): ?int
    {
        return match (true) {
            (int) config('tus.file_size_limit') > 0 => (int) config('tus.file_size_limit'),
            str_contains(ini_get('post_max_size'), 'M') => (int) ini_get('post_max_size') * 1000000,
            str_contains(ini_get('post_max_size'), 'G') => (int) ini_get('post_max_size') * 1000000000,
            default => null
        };
    }

    public function isInMaxFileSize(int $size): bool
    {
        $limit = $this->maxFileSize();

        if (is_null($limit)) {
            return true;
        }

        return $limit > $size;
    }

    public function extensionIsActive(string $extension): bool
    {
        return in_array($extension, (array) config('tus.extensions'));
    }

    public function id(): string
    {
        while (true) {

            $id = Str::random(40);

            if (!Tus::storage()->exists($this->path($id))) {
                break;
            }

        }

        return $id;
    }

    public function path(string $id): string
    {
        if (!empty(config('tus.storage_path'))) {
            return sprintf("%s/%s", config('tus.storage_path'), $id);
        }

        return $id;
    }

    public function storage(): Filesystem
    {
        return Storage::disk(config('tus.storage_disk'));
    }

    public function append(string $path, string $data): void
    {
        $path = $this->storage()->path($path);

        if (!is_writable($path)) {
            throw new Exception('writable');
        }


        if (!$fp = fopen($path, 'a')) {
            throw new Exception('open');
        }

        if (fwrite($fp, $data) === false) {
            throw new Exception('write');
        }

        fclose($fp);
    }
}
