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
     *
     */
    public function getHandicap(array $options): float;

}
