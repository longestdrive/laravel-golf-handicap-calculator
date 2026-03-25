<?php

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

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
        expect($calculator->getHandicap($options))->toBe(11);

        // Test case 2: Different values
        $options = [
            'actualHandicap' => 15.2,
            'courseSlope' => 125,
            'courseRating' => 71.5,
            'coursePar' => 70,
        ];

        // Expected: (15.2 * (125 / 100)) + ((71.5 - 70) / 2) = 19.0 + 0.75 = 19.75 (rounded to 20)
        expect($calculator->getHandicap($options))->toBe(20);
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
        expect($calculator->getHandicap($options))->toBe(0);

        // Test case 2: Negative handicap (for very good players)
        $options = [
            'actualHandicap' => -2.5,
            'courseSlope' => 130,
            'courseRating' => 73.0,
            'coursePar' => 72,
        ];

        // Expected: (-2.5 * (130 / 100)) + ((73.0 - 72) / 2) = -3.25 + 0.5 = -2.75 (rounded to -3)
        expect($calculator->getHandicap($options))->toBe(-3);
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

describe('SimpleHandicapCalculator reverseHandicapIndex', function () {
    it('reverse-calculates the handicap index from a playing handicap', function () {
        $calculator = new SimpleHandicapCalculator;

        // Test case 1: Standard slope of 100 — no slope adjustment, no rating/par difference
        // forward: (10.5 * (100/100)) + ((72.0-72)/2) = 10.5 → round = 11
        // reverse: (11 - (72.0-72)/2) * (100/100) = 11.0
        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => 11,
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]);
        expect($result)->toBe(11.0);

        // Test case 2: Non-standard slope with course rating above par
        // forward: (15.2 * (125/100)) + ((71.5-70)/2) = 19.0 + 0.75 = 19.75 → round = 20
        // reverse: (20 - (71.5-70)/2) * (100/125) = (20 - 0.75) * 0.8 = 19.25 * 0.8 = 15.4
        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => 20,
            'courseSlope' => 125,
            'courseRating' => 71.5,
            'coursePar' => 70,
        ]);
        expect($result)->toBeFloat();
        expect(round($result, 1))->toBe(15.4);
    });

    it('handles negative playing handicap', function () {
        $calculator = new SimpleHandicapCalculator;

        // forward: (-2.5 * (130/100)) + ((73.0-72)/2) = -3.25 + 0.5 = -2.75 → round = -3
        // reverse: (-3 - (73.0-72)/2) * (100/130) = (-3 - 0.5) * 0.7692 = -3.5 * 0.7692 = -2.6923
        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => -3,
            'courseSlope' => 130,
            'courseRating' => 73.0,
            'coursePar' => 72,
        ]);
        expect($result)->toBeFloat();
        expect(round($result, 4))->toBe(-2.6923);
    });

    it('returns null when courseSlope is zero', function () {
        $calculator = new SimpleHandicapCalculator;

        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'courseSlope' => 0,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]);
        expect($result)->toBeNull();
    });

    it('validates required options', function () {
        $calculator = new SimpleHandicapCalculator;

        expect(fn () => $calculator->reverseHandicapIndex([
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]))->toThrow(MissingOptionsException::class);

        expect(fn () => $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]))->toThrow(MissingOptionsException::class);
    });

    it('validates option types', function () {
        $calculator = new SimpleHandicapCalculator;

        // playingHandicap must be int
        expect(fn () => $calculator->reverseHandicapIndex([
            'playingHandicap' => 10.5,
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]))->toThrow(InvalidOptionsException::class);

        // actualHandicap is not a valid option for reverseHandicapIndex
        expect(fn () => $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'actualHandicap' => 10.5,
            'courseSlope' => 100,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]))->toThrow(UndefinedOptionsException::class);
    });
});
