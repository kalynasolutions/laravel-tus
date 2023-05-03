<?php

namespace KalynaSolutions\Tus\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
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
        $id = Tus::id();
        $path = Tus::path($id);

        Tus::storage()->put(
            path: $path,
            contents: Tus::extensionIsActive('creation-with-upload') && (int) $request->header('content-length') > 0 ? $request->getContent() : ''
        );

        Tus::metadata()->store(
            id: $id,
            rawMetadata: $request->header('upload-metadata'),
            customMetadata: [
                'size' => $request->header('upload-length'),
            ]
        );

        return response(
            status: 201,
            headers: Tus::headers()
                ->forPost(
                    id: $id,
                    offset: Tus::storage()->size($path),
                    lastModified: Tus::storage()->lastModified($path)
                )
                ->toArray()
        );
    }

    public function head(string $id): Response
    {
        $path = Tus::path($id);

        if (!Tus::storage()->exists($path)) {
            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );
        }

        return response(
            status: 200,
            headers: Tus::headers()
                ->forHead(
                    length: Tus::metadata()->readMeta($id, 'size'),
                    offset: Tus::storage()->size($path),
                    lastModified: Tus::storage()->lastModified($path)
                )
                ->toArray()
        );
    }

    public function patch(Request $request, string $id): Response
    {
        $path = Tus::path($id);

        if (!Tus::storage()->exists($path)) {
            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );
        }

        if (Tus::extensionIsActive('expiration') && Tus::isUploadExpired(Tus::storage()->lastModified($path))) {

            Tus::storage()->delete($path);

            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );

        }

        if ((int) $request->header('upload-offset') !== Tus::storage()->size($path)) {
            return response(
                status: 409,
                headers: Tus::headers()->default()->toArray()
            );
        }

        $offset = Tus::storage()->size($path) + Tus::append($path, $request->getContent());

        if ($offset === (int) Tus::metadata()->readMeta($id, 'size')) {
            logs()->info('COMPLETED '.$id);
        }

        return response(
            status: 204,
            headers: Tus::headers()
                ->forPatch(
                    offset: $offset,
                    lastModified: Tus::storage()->lastModified($path)
                )
                ->toArray()
        );
    }

    public function delete(string $id): Response
    {
        $path = Tus::path($id);

        if (!Tus::extensionIsActive('termination')) {
            $deleted = false;
        } elseif (Tus::storage()->exists($path)) {
            $deleted = Tus::storage()->delete($path);
        } else {
            $deleted = false;
        }

        return response(
            status: $deleted ? 204 : 404,
            headers: Tus::headers()->default()->toArray()
        );
    }
}
