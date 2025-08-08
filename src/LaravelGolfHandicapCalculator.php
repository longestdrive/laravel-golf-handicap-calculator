<?php

namespace Longestdrive\LaravelGolfHandicapCalculator;

use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorInterface;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;

/**
 * Main class for the Laravel Golf Handicap Calculator package.
 */
class LaravelGolfHandicapCalculator
{
    /**
     * The handicap calculator implementation.
     */
    protected HandicapCalculatorInterface $calculator;

    /**
     * Create a new LaravelGolfHandicapCalculator instance.
     */
    public function __construct(?HandicapCalculatorInterface $calculator = null)
    {
        $this->calculator = $calculator ?? new WHSHandicapCalculator;
    }

    /**
     * Calculate the playing handicap.
     *
     * @return float The calculated playing handicap
     */
    public function getHandicap(array $options): float
    {
        return $this->calculator->getHandicap($options);
    }

    /**
     * Set a different handicap calculator implementation.
     *
     * @return $this
     */
    public function setCalculator(HandicapCalculatorInterface $calculator): self
    {
        $this->calculator = $calculator;

        return $this;
    }
}
