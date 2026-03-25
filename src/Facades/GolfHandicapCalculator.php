<?php

namespace Longestdrive\LaravelGolfHandicapCalculator\Facades;

use Illuminate\Support\Facades\Facade;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;
use Longestdrive\LaravelGolfHandicapCalculator\LaravelGolfHandicapCalculator;

/**
 * @see LaravelGolfHandicapCalculator
 */
class GolfHandicapCalculator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HandicapCalculatorFactory::class;
    }
}
