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
        // (8.4 * (128 / 113)) + (70.2 - 72) =
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

        // - 8.4 * (128 / 100) = 8.4 * 1.28 = 10.752
        // - (70.2 - 72) / 2 = -1.8 / 2 = -0.9
        // - Sum = 10.752 + (-0.9) = 9.852
        // - round(9.852) = 10.0
        expect($handicap)->not()->toBeNull();
        expect(is_numeric($handicap))->toBeTrue();
        expect($handicap)->toBe(10);
    });

    it('reverse-calculates a WHS handicap index using the factory', function () {
        $factory = new HandicapCalculatorFactory;
        $calculator = $factory->make('whs');

        // forward: (8.4 * (128/113)) + (70.3 - 72) = 9.513 + (-1.7) = 7.813 → round = 8
        // reverse: (8 - (70.3 - 72)) * (113 / 128) = (8 + 1.7) * 0.8828 = 9.7 * 0.8828 = 8.563
        $handicapIndex = $calculator->reverseHandicapIndex([
            'playingHandicap' => 8,
            'coursePar' => 72,
            'courseRating' => 70.3,
            'courseSlope' => 128,
        ]);

        expect($handicapIndex)->not()->toBeNull();
        expect(is_float($handicapIndex))->toBeTrue();
        expect(round($handicapIndex, 3))->toBe(8.563);
    });

    it('reverse-calculates a Simple handicap index using the factory', function () {
        $factory = new HandicapCalculatorFactory;
        $factory->register('simple', SimpleHandicapCalculator::class);
        $calculator = $factory->make('simple');

        // forward: (8.4 * (128/100)) + ((70.2-72)/2) = 10.752 + (-0.9) = 9.852 → round = 10
        // reverse: (10 - (70.2-72)/2) * (100/128) = (10 + 0.9) * 0.78125 = 10.9 * 0.78125 = 8.516
        $handicapIndex = $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'coursePar' => 72,
            'courseRating' => 70.2,
            'courseSlope' => 128,
        ]);

        expect($handicapIndex)->not()->toBeNull();
        expect(is_float($handicapIndex))->toBeTrue();
        expect(round($handicapIndex, 3))->toBe(8.516);
    });
});
