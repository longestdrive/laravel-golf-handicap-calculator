<?php

namespace Longestdrive\LaravelGolfHandicapCalculator;

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Longestdrive\LaravelGolfHandicapCalculator\Commands\LaravelGolfHandicapCalculatorCommand;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorInterface;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;

class LaravelGolfHandicapCalculatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-golf-handicap-calculator')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(HandicapCalculatorFactory::class, function ($app) {
            $factory = new HandicapCalculatorFactory();
            // Optionally register default calculators here
            $factory->register('whs', WHSHandicapCalculator::class);
            return $factory;
        });
    }
}
