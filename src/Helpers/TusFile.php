<?php

namespace KalynaSolutions\Tus\Helpers;

use Illuminate\Support\Str;
use KalynaSolutions\Tus\Exceptions\FileNotFoundException;
use KalynaSolutions\Tus\Facades\Tus;

readonly class TusFile
{
    public string $id;
    public string $path;
    public string $disk;

    public array $metadata;

    public function __construct(string $id, string $path, array $metadata)
    {
        $this->id = $id;
        $this->path = $path;
        $this->disk = config('tus.storage_disk');
        $this->metadata = $metadata;
    }

    public static function create(string $id = null, int $size = 0, ?string $rawMetadata = null): static
    {
        if (!$id) {
            $id = Tus::id();
        }

        return new static(
            id: $id,
            path: Tus::path($id),
            metadata: Tus::metadata()->store(
                id: $id,
                rawMetadata: $rawMetadata,
                customMetadata: [
                    'size' => $size,
                ]
            )
        );
    }

    public static function find(string $id): static
    {
        $path = Tus::path($id);

        if (!Tus::storage()->exists($path)) {
            throw new FileNotFoundException;
        }

        return new static(
            id: $id,
            path: $path,
            metadata: Tus::metadata()->read($id)
        );
    }
}