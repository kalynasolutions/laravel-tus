<?php

namespace KalynaSolutions\Tus;

use KalynaSolutions\Tus\Commands\TusClearExpiredUploadsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-tus')
            ->hasConfigFile('tus')
            ->hasRoute('tus')
            ->hasCommand(TusClearExpiredUploadsCommand::class);
    }
}
