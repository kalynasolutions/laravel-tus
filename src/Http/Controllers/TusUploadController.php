<?php

namespace KalynaSolutions\Tus\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use KalynaSolutions\Tus\Facades\Tus;

class TusUploadController extends BaseController
{
    public function options(): Response
    {
        return response(
            status: 204,
            headers: Tus::headers()->forOptions()->toArray()
        );
    }

    public function post(Request $request): Response
    {
        $id = Str::random(40);

        Storage::disk(config('tus.storage_disk'))->put(
            path: $id,
            contents: Tus::extensionIsActive('creation-with-upload') && (int) $request->header('content-length') > 0 ? $request->getContent() : ''
        );

        return response(
            status: 201,
            headers: Tus::headers()
                ->forPost(
                    id: $id,
                    offset: Storage::disk(config('tus.storage_disk'))->size($id),
                    lastModified: Storage::disk(config('tus.storage_disk'))->lastModified($id)
                )
                ->toArray()
        );
    }

    public function head(string $id): Response
    {
        if (! Storage::disk(config('tus.storage_disk'))->exists($id)) {
            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );
        }

        return response(
            status: 200,
            headers: Tus::headers()
                ->forHead(
                    offset: Storage::disk(config('tus.storage_disk'))->size($id),
                    lastModified: Storage::disk(config('tus.storage_disk'))->lastModified($id)
                )
                ->toArray()
        );
    }

    public function patch(Request $request, string $id): Response
    {
        if (! Storage::disk(config('tus.storage_disk'))->exists($id)) {
            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );
        }

        if (Tus::extensionIsActive('expiration') && Tus::isUploadExpired(Storage::disk(config('tus.storage_disk'))->lastModified($id))) {

            Storage::disk(config('tus.storage_disk'))->delete($id);

            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );

        }

        if ((int) $request->header('upload-offset') !== Storage::disk(config('tus.storage_disk'))->size($id)) {
            return response(
                status: 409,
                headers: Tus::headers()->default()->toArray()
            );
        }

        Storage::disk(config('tus.storage_disk'))->append($id, $request->getContent(), null);

        return response(
            status: 204,
            headers: Tus::headers()
                ->forPatch(
                    offset: Storage::disk(config('tus.storage_disk'))->size($id),
                    lastModified: Storage::disk(config('tus.storage_disk'))->lastModified($id)
                )
                ->toArray()
        );
    }

    public function delete(string $id): Response
    {
        if (! Tus::extensionIsActive('termination')) {
            $deleted = false;
        } elseif (Storage::disk(config('tus.storage_disk'))->exists($id)) {
            $deleted = Storage::disk(config('tus.storage_disk'))->delete($id);
        } else {
            $deleted = false;
        }

        return response(
            status: $deleted ? 204 : 404,
            headers: Tus::headers()->default()->toArray()
        );
    }
}
