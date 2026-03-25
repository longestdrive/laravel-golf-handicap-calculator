<?php

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

describe('WHSHandicapCalculator', function () {
    it('calculates handicap correctly with valid inputs', function () {
        $calculator = new WHSHandicapCalculator;

        // Test case 1: Standard values
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 113,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        // Expected: (10.5 * (113 / 113)) + (72.0 - 72) = 10.5 + 0 = 10.5 (rounded to 11)
        expect($calculator->getHandicap($options))->toBe(11);

        // Test case 2: Different values
        $options = [
            'actualHandicap' => 15.2,
            'courseSlope' => 125,
            'courseRating' => 71.5,
            'coursePar' => 70,
        ];

        // Expected: (15.2 * (125 / 113)) + (71.5 - 70) = 16.8 + 1.5 = 18.3 (rounded to 18)
        expect($calculator->getHandicap($options))->toBe(18);
    });

    it('handles edge cases correctly', function () {
        $calculator = new WHSHandicapCalculator;

        // Test case 1: Zero handicap
        $options = [
            'actualHandicap' => 0.0,
            'courseSlope' => 113,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ];

        // Expected: (0 * (113 / 113)) + (72.0 - 72) = 0 + 0 = 0
        expect($calculator->getHandicap($options))->toBe(0);

        // Test case 2: Negative handicap (for very good players)
        $options = [
            'actualHandicap' => -2.5,
            'courseSlope' => 130,
            'courseRating' => 73.0,
            'coursePar' => 72,
        ];

        // Expected: (-2.5 * (130 / 113)) + (73.0 - 72) = -2.88 + 1 = -1.88 (rounded to -2)
        expect($calculator->getHandicap($options))->toBe(-2);

        // Test case 3: Course slope equals minimum value (55)
        $options = [
            'actualHandicap' => 10.0,
            'courseSlope' => 55,
            'courseRating' => 70.0,
            'coursePar' => 72,
        ];

        // Expected: (10 * (55 / 113)) + (70.0 - 72) = 4.87 - 2 = 2.87 (rounded to 3)
        expect($calculator->getHandicap($options))->toBe(3);

        // Test case 4: Course slope equals maximum value (155)
        $options = [
            'actualHandicap' => 10.0,
            'courseSlope' => 155,
            'courseRating' => 70.0,
            'coursePar' => 72,
        ];

        // Expected: (10 * (155 / 113)) + (70.0 - 72) = 13.72 - 2 = 11.72 (rounded to 12)
        expect($calculator->getHandicap($options))->toBe(12);
    });

    it('validates required options', function () {
        $calculator = new WHSHandicapCalculator;

        // Test case 1: Missing actualHandicap
        $options = [
            'courseSlope' => 113,
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
            'courseSlope' => 113,
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(MissingOptionsException::class);

        // Test case 4: Missing coursePar
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 113,
            'courseRating' => 72.0,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(MissingOptionsException::class);
    });

    it('validates option types', function () {
        $calculator = new WHSHandicapCalculator;

        // Test case 1: Invalid actualHandicap type (string instead of float)
        $options = [
            'actualHandicap' => 'invalid',
            'courseSlope' => 113,
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
            'courseSlope' => 113,
            'courseRating' => 'invalid',
            'coursePar' => 72,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(InvalidOptionsException::class);

        // Test case 4: Invalid coursePar type (float instead of int)
        $options = [
            'actualHandicap' => 10.5,
            'courseSlope' => 113,
            'courseRating' => 72.0,
            'coursePar' => 72.5,
        ];

        expect(fn () => $calculator->getHandicap($options))->toThrow(InvalidOptionsException::class);
    });
});

describe('WHSHandicapCalculator reverseHandicapIndex', function () {
    it('reverse-calculates the handicap index from a playing handicap', function () {
        $calculator = new WHSHandicapCalculator;

        // Test case 1: Standard reference slope — no adjustment needed
        // forward: (10.0 * (113 / 113)) + (72.0 - 72) = 10.0 → round = 10
        // reverse: (10 - (72.0 - 72)) * (113 / 113) = 10.0
        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'courseSlope' => 113,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]);
        expect($result)->toBe(10.0);

        // Test case 2: Non-standard slope with course rating above par
        // forward: (15.2 * (125/113)) + (71.5-70) = 16.814 + 1.5 = 18.314 → round = 18
        // reverse: (18 - (71.5 - 70)) * (113 / 125) = 16.5 * 0.904 = 14.916
        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => 18,
            'courseSlope' => 125,
            'courseRating' => 71.5,
            'coursePar' => 70,
        ]);
        expect($result)->toBeFloat();
        expect(round($result, 3))->toBe(14.916);
    });

    it('handles negative playing handicap', function () {
        $calculator = new WHSHandicapCalculator;

        // forward: (-2.5 * (130/113)) + (73.0-72) = -2.876 + 1 = -1.876 → round = -2
        // reverse: (-2 - (73.0 - 72)) * (113 / 130) = (-2 - 1) * 0.8692 = -3 * 0.8692 = -2.6077
        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => -2,
            'courseSlope' => 130,
            'courseRating' => 73.0,
            'coursePar' => 72,
        ]);
        expect($result)->toBeFloat();
        expect(round($result, 4))->toBe(-2.6077);
    });

    it('returns null when courseSlope is zero', function () {
        $calculator = new WHSHandicapCalculator;

        $result = $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'courseSlope' => 0,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]);
        expect($result)->toBeNull();
    });

    it('validates required options', function () {
        $calculator = new WHSHandicapCalculator;

        expect(fn () => $calculator->reverseHandicapIndex([
            'courseSlope' => 113,
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
        $calculator = new WHSHandicapCalculator;

        // playingHandicap must be int
        expect(fn () => $calculator->reverseHandicapIndex([
            'playingHandicap' => 10.5,
            'courseSlope' => 113,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]))->toThrow(InvalidOptionsException::class);

        // actualHandicap is not a valid option for reverseHandicapIndex
        expect(fn () => $calculator->reverseHandicapIndex([
            'playingHandicap' => 10,
            'actualHandicap' => 10.5,
            'courseSlope' => 113,
            'courseRating' => 72.0,
            'coursePar' => 72,
        ]))->toThrow(UndefinedOptionsException::class);
    });
});
