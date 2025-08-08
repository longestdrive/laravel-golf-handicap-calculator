<?php

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorInterface;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;

describe('HandicapCalculatorFactory', function () {
    it('returns the default WHSHandicapCalculator for key "whs"', function () {
        $factory = new HandicapCalculatorFactory;
        $calculator = $factory->make('whs');
        expect($calculator)->toBeInstanceOf(WHSHandicapCalculator::class);
        expect($calculator)->toBeInstanceOf(HandicapCalculatorInterface::class);
    });

    it('can register and return a SimpleHandicapCalculator', function () {
        $factory = new HandicapCalculatorFactory;
        $factory->register('simple', SimpleHandicapCalculator::class);
        $calculator = $factory->make('simple');
        expect($calculator)->toBeInstanceOf(SimpleHandicapCalculator::class);
        expect($calculator)->toBeInstanceOf(HandicapCalculatorInterface::class);
    });

    it('throws an exception for unknown calculator key', function () {
        $factory = new HandicapCalculatorFactory;
        expect(fn () => $factory->make('unknown'))
            ->toThrow(Exception::class);
    });
});
