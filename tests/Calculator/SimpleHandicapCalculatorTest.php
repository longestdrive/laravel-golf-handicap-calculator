<?php

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

describe('SimpleHandicapCalculator', function () {
    it('calculates handicap correctly with valid inputs', function () {
        $calculator = new SimpleHandicapCalculator;

        // Test case 1: Standard values
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        // Expected: (10.5 * (100 / 100)) + ((72.0 - 72) / 2) = 10.5 + 0 = 10.5 (rounded to 11)
        expect($calculator->getHandicap($options))->toBe(11.0);

        // Test case 2: Different values
        $options = [
            'actualHandicap' => 15.2,
            'courseSlope' => 125,
            'courseRating' => 71.5,
            'coursePar' => 70,
        ];

        // Expected: (15.2 * (125 / 100)) + ((71.5 - 70) / 2) = 19.0 + 0.75 = 19.75 (rounded to 20)
        expect($calculator->getHandicap($options))->toBe(20.0);
    });

    it('handles edge cases correctly', function () {
        $calculator = new SimpleHandicapCalculator;

        // Test case 1: Zero handicap
        $options = [
            'actualHandicap' => 0.0,
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        // Expected: (0 * (100 / 100)) + ((72.0 - 72) / 2) = 0 + 0 = 0
        expect($calculator->getHandicap($options))->toBe(0.0);

        // Test case 2: Negative handicap (for very good players)
        $options = [
            'actualHandicap' => -2.5,
            'courseSlope' => 130,
            'courseRating' => 73.0,
            'coursePar' => 72,
        ];

        // Expected: (-2.5 * (130 / 100)) + ((73.0 - 72) / 2) = -3.25 + 0.5 = -2.75 (rounded to -3)
        expect($calculator->getHandicap($options))->toBe(-3.0);
    });

    it('validates required options', function () {
        $calculator = new SimpleHandicapCalculator;

        // Test case 1: Missing actualHandicap
        $options = [
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(MissingOptionsException::class);

        // Test case 2: Missing courseSlope
        $options = [
            'actualHandicap' => 10.5,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(MissingOptionsException::class);

        // Test case 3: Missing courseRating
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 100,
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(MissingOptionsException::class);

        // Test case 4: Missing coursePar
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 100,
            'courseRating' => 72.0,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(MissingOptionsException::class);
    });

    it('validates option types', function () {
        $calculator = new SimpleHandicapCalculator;

        // Test case 1: Invalid actualHandicap type (string instead of float)
        $options = [
            'actualHandicap' => 'invalid',
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(InvalidOptionsException::class);

        // Test case 2: Invalid courseSlope type (string instead of int)
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 'invalid',
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(InvalidOptionsException::class);

        // Test case 3: Invalid courseRating type (string instead of float)
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 100,
            'courseRating' => 'invalid',
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(InvalidOptionsException::class);

        // Test case 4: Invalid coursePar type (float instead of int)
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72.5,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(InvalidOptionsException::class);
    });
});
