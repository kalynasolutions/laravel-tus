<?php

namespace KalynaSolutions\Tus;

use KalynaSolutions\Tus\Commands\TusCommand;
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
            ->hasConfigFile()
            ->hasMigration('create_laravel-tus_table')
            ->hasRoute('tus')
            ->hasCommand(TusCommand::class);
    }
}
