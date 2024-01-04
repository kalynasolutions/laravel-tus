<?php

use Illuminate\Support\Facades\Route;
use KalynaSolutions\Tus\Http\Controllers\TusUploadController;
use KalynaSolutions\Tus\Http\Middleware\ValidateChecksumMiddleware;
use KalynaSolutions\Tus\Http\Middleware\ValidateFileSizeMiddleware;
use KalynaSolutions\Tus\Http\Middleware\ValidateVersionMiddleware;

Route::controller(TusUploadController::class)
    ->middleware(config('tus.middleware'))
    ->prefix(config('tus.path'))
    ->name('tus.')
    ->group(function () {

        Route::match('options', '/', 'options')->name('options');

        Route::match('post', '/', 'post')->name('post')
            ->middleware(ValidateFileSizeMiddleware::class)
            ->middleware(ValidateChecksumMiddleware::class);

        Route::match('head', '/{id}', 'head')->name('head');

        Route::match('patch', '/{id}', 'patch')->name('patch')
            ->middleware(ValidateFileSizeMiddleware::class)
            ->middleware(ValidateChecksumMiddleware::class);

        Route::match('delete', '/{id}', 'delete')->name('delete');

    });
