<?php

namespace Longestdrive\LaravelGolfHandicapCalculator\Calculator;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SimpleHandicapCalculator
 *
 * A simple implementation of the HandicapCalculatorInterface that uses a basic formula.
 * This is provided as an example of how to implement alternative calculation strategies.
 */
class SimpleHandicapCalculator implements HandicapCalculatorInterface
{
    private array $options;

    /**
     * Calculate the playing handicap using a simplified formula.
     * This is just a demonstration of an alternative implementation.
     *
     * @param  array  $options  Array containing calculation parameters
     * @return float The calculated playing handicap
     */
    public function getHandicap(array $options): int
    {
        $this->setCalculationOptions($options);

        return (int) $this->calculateSimpleHandicap();
    }

    /**
     * Calculate the handicap using a simplified formula
     */
    private function calculateSimpleHandicap(): float
    {
        // A simplified formula that doesn't use the WHS standard calculation
        // This is just for demonstration purposes
        return round($this->options['actualHandicap'] * ($this->options['courseSlope'] / 100) +
                    ($this->options['courseRating'] - $this->options['coursePar']) / 2);
    }

    /**
     * Validate and set calculation options
     */
    private function setCalculationOptions(array $options): void
    {
        $resolver = new OptionsResolver;

        // Define required options
        $resolver->setRequired([
            'actualHandicap',
            'courseSlope',
            'courseRating',
            'coursePar',
        ]);

        // Define allowed types for each option
        $resolver->setAllowedTypes('actualHandicap', ['float', 'int']);
        $resolver->setAllowedTypes('courseSlope', ['float', 'int']);
        $resolver->setAllowedTypes('courseRating', ['float', 'int']);
        $resolver->setAllowedTypes('coursePar', 'int');

        $this->options = $resolver->resolve($options);
    }
}
