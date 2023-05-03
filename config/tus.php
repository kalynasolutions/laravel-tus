<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tus metadata processing driver
    |--------------------------------------------------------------------------
    |
    | Configure how package must store and process upload metadata.
    |
    | Supported drivers: "file"
    |
    */
    'driver' => env('TUS_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Default endpoint path
    |--------------------------------------------------------------------------
    */
    'path' => env('TUS_PATH', 'tus'),

    /*
    |--------------------------------------------------------------------------
    | Default endpoint middlewares
    |--------------------------------------------------------------------------
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Default storage for files
    |--------------------------------------------------------------------------
    */
    'storage_disk' => env('TUS_STORAGE_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default tus directory path
    |--------------------------------------------------------------------------
    */
    'storage_path' => env('TUS_STORAGE_PATH', 'tus'),

    /*
    |--------------------------------------------------------------------------
    | Configure upload file size limit
    |--------------------------------------------------------------------------
    |
    | Set max file upload size limit in bytes. If `null` will be user php ini value from `max_post_size`.
    |
    */
    'file_size_limit' => null,

    /*
    |--------------------------------------------------------------------------
    | Default upload expiration
    |--------------------------------------------------------------------------
    |
    | This values used by `expiration` extension if it is enabled. If the file has expired, it will be deleted automatically.
    | If you not need file deletion after given time, set this value to `null`.
    |
    */
    'upload_expiration' => env('TUS_UPLOAD_EXPIRATION', 30),

    /*
    |--------------------------------------------------------------------------
    | Supported checksum algorithms for checksum extension
    |--------------------------------------------------------------------------
    |
    | All supported algos: https://www.php.net/manual/ru/function.hash-algos.php
    |
    */
    'checksum_algorithm' => ['crc32', 'md5', 'sha1'],

    /*
    |--------------------------------------------------------------------------
    | Tus extensions
    |--------------------------------------------------------------------------
    |
    | You can enable or disable specific tus.io protocol extensions on server.
    |
    | Supported extensions: "creation", "creation-with-upload", "expiration", "checksum", "termination"
    |
    */
    'extensions' => [
        'creation',
        'creation-with-upload',
        'expiration',
        'checksum',
        'termination',
    ],
];
