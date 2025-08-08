<?php

namespace Longestdrive\LaravelGolfHandicapCalculator\Facades;

use Illuminate\Support\Facades\Facade;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;

/**
 * @see \Longestdrive\LaravelGolfHandicapCalculator\LaravelGolfHandicapCalculator
 */
class GolfHandicapCalculator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HandicapCalculatorFactory::class;
    }
}
