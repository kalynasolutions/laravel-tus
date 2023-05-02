# Laravel Tus Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kalynasolutions/laravel-tus.svg?style=flat-square)](https://packagist.org/packages/kalynasolutions/laravel-tus)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kalynasolutions/laravel-tus/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kalynasolutions/laravel-tus/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kalynasolutions/laravel-tus/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kalynasolutions/laravel-tus/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kalynasolutions/laravel-tus.svg?style=flat-square)](https://packagist.org/packages/kalynasolutions/laravel-tus)

Laravel package for handling resumable file uploads with tus protocol and native [Uppy.js](https://uppy.io) support **without** additional tus servers.

> 100% Tus.io resumable upload [protocol](https://tus.io/protocols/resumable-upload.html) support

| Laravel Version | Package Version | Status |
|:---------------:|:---------------:|:------:|
|      10.x       |      ^1.0       |   ✅    |

## Installation

You can install the package via composer:

```bash
composer require kalynasolutions/laravel-tus
```

You can publish and run the migrations with (optional):

```bash
php artisan vendor:publish --tag="laravel-tus-migrations"
php artisan migrate
```

You can publish the config file with (optional):

```bash
php artisan vendor:publish --tag="laravel-tus-config"
```

## Usage with Uppy.js

> You can use this package with other tus libraries, package fully implemented with Tus.io protocol RFC.

```js
import Uppy, { debugLogger } from "@uppy/core";
import Tus from "@uppy/tus";

const TUS_ENDPOINT = "https://site.test/tus";
const TUS_LIMIT = 5;
const TUS_CHUNK_SIZE = 20000000;


const uppy = new Uppy({ logger: debugLogger });

uppy.use(Tus, { endpoint: TUS_ENDPOINT, limit: TUS_LIMIT, chunkSize: TUS_CHUNK_SIZE })
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Artur Khylskyi](https://github.com/arthurpatriot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
