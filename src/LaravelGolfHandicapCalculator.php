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
     *
     * @var HandicapCalculatorInterface
     */
    protected HandicapCalculatorInterface $calculator;

    /**
     * Create a new LaravelGolfHandicapCalculator instance.
     *
     * @param HandicapCalculatorInterface|null $calculator
     */
    public function __construct(HandicapCalculatorInterface $calculator = null)
    {
        $this->calculator = $calculator ?? new WHSHandicapCalculator();
    }

    /**
     * Calculate the playing handicap.
     *
     * @param array $options
     * @return float The calculated playing handicap
     */
    public function getHandicap(array $options): float
    {
        return $this->calculator->getHandicap($options);
    }

    /**
     * Set a different handicap calculator implementation.
     *
     * @param HandicapCalculatorInterface $calculator
     * @return $this
     */
    public function setCalculator(HandicapCalculatorInterface $calculator): self
    {
        $this->calculator = $calculator;

        return $this;
    }
}
