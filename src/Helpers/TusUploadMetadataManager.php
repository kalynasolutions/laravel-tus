<?php

namespace KalynaSolutions\Tus\Helpers;

use Illuminate\Support\Str;
use KalynaSolutions\Tus\Facades\Tus;

class TusUploadMetadataManager
{
    public function parse(string $rawMetadata): array
    {
        return str($rawMetadata)
            ->explode(',')
            ->mapWithKeys(function (string $data) {
                $data = explode(' ', $data);

                return [$data[ 0 ] => base64_decode($data[ 1 ])];
            })
            ->toArray();
    }

    public function store(string $id, ?string $rawMetadata = null, array $metadata = []): array
    {
        if (!empty($rawMetadata)) {
            $metadata = [...$metadata, ...$this->parse($rawMetadata)];
        }

        if (!isset($metadata[ 'extension' ])) {
            $extension = Str::afterLast($metadata[ 'name' ], '.');
            $metadata[ 'extension' ] = empty($extension) ? null : $extension;
        }

        match (config('tus.driver')) {
            default => $this->storeInFile($id, $metadata),
        };

        return $metadata;
    }

    protected function storeInFile(string $id, array $metadata): void
    {
        Tus::storage()->put(Tus::path($id, 'json'), json_encode($metadata));
    }

    public function read(string $id): array
    {
        return match (config('tus.driver')) {
            default => $this->readFromFile($id),
        };
    }

    protected function readFromFile(string $id): array
    {
        return Tus::storage()->json(Tus::path($id, 'json')) ?? [];
    }

    public function readMeta(string $id, string $key): mixed
    {
        return $this->read($id)[ $key ] ?? null;
    }
}
