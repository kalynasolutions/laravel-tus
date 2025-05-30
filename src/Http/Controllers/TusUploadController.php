<?php

namespace KalynaSolutions\Tus\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use KalynaSolutions\Tus\Events\FileUploadBeforeCreated;
use KalynaSolutions\Tus\Events\FileUploadCreated;
use KalynaSolutions\Tus\Events\FileUploadFinished;
use KalynaSolutions\Tus\Events\FileUploadStarted;
use KalynaSolutions\Tus\Facades\Tus;
use KalynaSolutions\Tus\Helpers\TusFile;

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
        event(new FileUploadBeforeCreated($request));

        $tusFile = TusFile::create(
            size: $request->header('upload-length', 0),
            rawMetadata: $request->header('upload-metadata')
        );

        $contents = Tus::extensionIsActive('creation-with-upload') && (int) $request->header('content-length') > 0 ? $request->getContent(true) : '';

        Tus::storage()->put(
            path: $tusFile->path,
            contents: $contents
        );

        event(new FileUploadCreated($tusFile));

        if (! empty($contents)) {
            event(new FileUploadStarted($tusFile));
        }

        return response(
            status: 201,
            headers: Tus::headers()->forPost($tusFile)->toArray()
        );
    }

    public function head(string $id): Response
    {
        $tusFile = TusFile::find($id);

        return response(
            status: 200,
            headers: Tus::headers()->forHead($tusFile)->toArray()
        );
    }

    public function patch(Request $request, string $id): Response
    {
        $tusFile = TusFile::find($id);

        if (Tus::extensionIsActive('expiration') && Tus::isUploadExpired(Tus::storage()->lastModified($tusFile->path))) {

            Tus::storage()->delete($tusFile->path);

            return response(
                status: 404,
                headers: Tus::headers()->default()->toArray()
            );

        }

        if ((int) $request->header('upload-offset') !== Tus::storage()->size($tusFile->path)) {
            return response(
                status: 409,
                headers: Tus::headers()->default()->toArray()
            );
        }

        if ((int) $request->header('upload-offset') === 0) {
            event(new FileUploadStarted($tusFile));
        }

        $offset = Tus::storage()->size($tusFile->path) + Tus::append($tusFile->path, $request->getContent(true));
        $lastModified = Tus::storage()->lastModified($tusFile->path);

        if ($offset === (int) Tus::metadata()->readMeta($id, 'size')) {
            event(new FileUploadFinished($tusFile));
        }

        return response(
            status: 204,
            headers: Tus::headers()
                ->forPatch(
                    offset: $offset,
                    lastModified: $lastModified
                )
                ->toArray()
        );
    }

    public function delete(string $id): Response
    {
        $tusFile = TusFile::find($id);

        if (! Tus::extensionIsActive('termination')) {
            $deleted = false;
        } else {
            $deleted = Tus::storage()->delete($tusFile->path);
        }

        return response(
            status: $deleted ? 204 : 404,
            headers: Tus::headers()->default()->toArray()
        );
    }
}
