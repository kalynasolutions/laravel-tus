<?php

return [
    'driver' => env('TUS_DRIVER', 'file'),

    'path' => env('TUS_PATH', 'tus'),

    'middleware' => ['web'],

    'storage_disk' => env('TUS_STORAGE_DISK', 'local'),

    'file_size_limit' => null,

    'upload_expiration' => env('TUS_UPLOAD_EXPIRATION', 30),

    'checksum_algorithm' => ['crc32', 'md5', 'sha1'],

    'extensions' => [
        'creation',
        'creation-with-upload',
        'expiration',
        'checksum',
        'termination',
    ],
];
