<?php

namespace KalynaSolutions\Tus\Helpers;

use KalynaSolutions\Tus\Facades\Tus;

class TusUploadMetadataManager
{
    public function parse(string $rawMetadata): array
    {
        return str($rawMetadata)
            ->explode(',')
            ->mapWithKeys(function (string $data) {
                $data = explode(' ', $data);

                return [$data[0] => base64_decode($data[1])];
            })
            ->toArray();
    }

    public function store(string $id, ?string $rawMetadata = null, array $customMetadata = []): array
    {
        if (! empty($rawMetadata)) {
            $customMetadata = [...$customMetadata, ...$this->parse($rawMetadata)];
        }

        match (config('tus.driver')) {
            default => $this->storeInFile($id, $customMetadata),
        };

        return $customMetadata;
    }

    protected function storeInFile(string $id, array $metadata): void
    {
        $path = sprintf('%s.json', Tus::path($id));

        Tus::storage()->put($path, json_encode($metadata));
    }

    public function read(string $id): array
    {
        return match (config('tus.driver')) {
            default => $this->readFromFile($id),
        };
    }

    protected function readFromFile(string $id): array
    {
        $path = sprintf('%s.json', Tus::path($id));

        return Tus::storage()->json($path) ?? [];
    }

    public function readMeta(string $id, string $key): mixed
    {
        return $this->read($id)[$key] ?? null;
    }
}
