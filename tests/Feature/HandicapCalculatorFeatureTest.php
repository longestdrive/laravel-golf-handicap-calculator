<?php

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;

describe('HandicapCalculator feature', function () {
    it('calculates a WHS handicap using the factory', function () {
        $factory = new HandicapCalculatorFactory();
        $calculator = $factory->make('whs');
        $handicap = $calculator->getHandicap([
            "actualHandicap" =>8.4,
            "coursePar" =>72,
            "courseRating" =>128,
            "courseSlope" =>70.2
        ]);
        expect($handicap)->not()->toBeNull();
        expect(is_numeric($handicap))->toBeTrue();
    });

    it('registers and calculates a Simple handicap using the factory', function () {
        $factory = new HandicapCalculatorFactory();
        $factory->register('simple', SimpleHandicapCalculator::class);
        $calculator = $factory->make('simple');
        $handicap = $calculator->getHandicap([
            "actualHandicap" =>8.4,
            "coursePar" =>72,
            "courseRating" =>128,
            "courseSlope" =>70.2
        ]);
        expect($handicap)->not()->toBeNull();
        expect(is_numeric($handicap))->toBeTrue();
    });
});

