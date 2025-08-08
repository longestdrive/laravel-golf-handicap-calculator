<?php

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;

describe('HandicapCalculator feature', function () {
    it('calculates a WHS handicap using the factory', function () {
        $factory = new HandicapCalculatorFactory;
        $calculator = $factory->make('whs');
        $handicap = $calculator->getHandicap([
            'actualHandicap' => 8.4,
            'coursePar' => 72,
            'courseRating' => 70.3,
            'courseSlope' => 128,
        ]);
        expect($handicap)->not()->toBeNull();
        expect(is_numeric($handicap))->toBeTrue();
        //(8.4 * (128 / 113)) + (70.2 - 72) =
        expect($handicap)->toBe(8);

    });

    it('registers and calculates a Simple handicap using the factory', function () {
        $factory = new HandicapCalculatorFactory;
        $factory->register('simple', SimpleHandicapCalculator::class);
        $calculator = $factory->make('simple');
        $handicap = $calculator->getHandicap([
            'actualHandicap' => 8.4,
            'coursePar' => 72,
            'courseRating' => 70.2,
            'courseSlope' => 128,
        ]);

        //- 8.4 * (128 / 100) = 8.4 * 1.28 = 10.752
        //- (70.2 - 72) / 2 = -1.8 / 2 = -0.9
        //- Sum = 10.752 + (-0.9) = 9.852
        //- round(9.852) = 10.0
        expect($handicap)->not()->toBeNull();
        expect(is_numeric($handicap))->toBeTrue();
        expect($handicap)->toBe(10);
    });
});
