<?php

namespace Longestdrive\LaravelGolfHandicapCalculator\Calculator;

/**
 * Interface HandicapCalculatorInterface
 *
 * Defines the contract for handicap calculation strategies.
 */
interface HandicapCalculatorInterface
{
    /**
     * Calculate the playing handicap based on the actual handicap,
     * course slope, course rating, and course par.
     */
    public function getHandicap(array $options): int;

    /**
     * Reverse-calculate the handicap index from a playing handicap,
     * course slope, course rating, and course par.
     * Returns null if the calculation is not possible (e.g. courseSlope is zero).
     */
    public function reverseHandicapIndex(array $options): ?float;
}
