<?php

namespace KalynaSolutions\Tus\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Date;
use KalynaSolutions\Tus\Facades\Tus;

class TusHeaderBuilder implements Arrayable
{
    protected string $version;

    protected array $headers;

    public function __construct(string $version)
    {
        $this->version = $version;
        $this->headers = [];
    }

    /**
     * @return $this
     */
    public function version(): static
    {
        $this->headers['Tus-Version'] = $this->version;

        return $this;
    }

    /**
     * @return $this
     */
    public function resumable(): static
    {
        $this->headers['Tus-Resumable'] = $this->version;

        return $this;
    }

    /**
     * @return $this
     */
    public function offset(int $offset): static
    {
        $this->headers['Upload-Offset'] = $offset;

        return $this;
    }

    /**
     * @return $this
     */
    public function maxSize(): static
    {
        $this->headers['Tus-Max-Size'] = Tus::maxFileSize();

        return $this;
    }

    /**
     * @return $this
     */
    public function extensions(): static
    {
        $extensions = config('tus.extensions');

        if (! is_array($extensions) || empty($extensions)) {
            return $this;
        }

        $this->headers['Tus-Extension'] = implode(',', $extensions);

        return $this;
    }

    /**
     * @return $this
     */
    public function location(string $id): static
    {
        $this->headers['Location'] = route('tus.patch', $id);

        return $this;
    }

    /**
     * @return $this
     */
    public function expires(int $lastModified): static
    {
        if (! Tus::extensionIsActive('expiration')) {
            return $this;
        }

        $this->headers['Upload-Expires'] = Date::createFromTimestamp($lastModified)->addMinutes((int) config('tus.upload_expiration'))->toRfc7231String();

        return $this;
    }

    /**
     * @return $this
     */
    public function checksumAlgorithm(): static
    {
        if (! Tus::extensionIsActive('checksum')) {
            return $this;
        }

        $this->headers['Tus-Checksum-Algorithm'] = implode(',', (array) config('tus.checksum_algorithm'));

        return $this;
    }

    public function length(int $length): static
    {
        if ($length === 0) {
            return $this;
        }

        $this->headers['Upload-Length'] = $length;

        return $this;
    }

    /**
     * @return $this
     */
    public function forOptions(): static
    {
        $this->resumable()->version()->maxSize()->extensions()->checksumAlgorithm();

        return $this;
    }

    /**
     * @return $this
     */
    public function forPost(TusFile $tusFile): static
    {
        $this->resumable()
            ->location($tusFile->id)
            ->offset(Tus::storage()->size($tusFile->path))
            ->expires(Tus::storage()->lastModified($tusFile->path))
            ->maxSize();

        return $this;
    }

    /**
     * @return $this
     */
    public function forHead(TusFile $tusFile): static
    {
        $this->resumable()
            ->length($tusFile->metadata['size'])
            ->offset(Tus::storage()->size($tusFile->path))
            ->expires(Tus::storage()->lastModified($tusFile->path));

        return $this;
    }

    /**
     * @return $this
     */
    public function forPatch(int $offset, int $lastModified): static
    {
        $this->resumable()->offset($offset)->expires($lastModified);

        return $this;
    }

    /**
     * @return $this
     */
    public function default(): static
    {
        $this->resumable();

        return $this;
    }

    public function toArray(): array
    {
        return $this->headers;
    }
}
