<?php

namespace Longestdrive\LaravelGolfHandicapCalculator\Calculator;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WHSHandicapCalculator
 *
 * A class that calculates the playing handicap based on the actual handicap,
 * course slope, course rating, and course par using the World Handicap System (WHS) formula.
 */
class WHSHandicapCalculator implements HandicapCalculatorInterface
{
    private array $options;

    /**
     * @param array $options
     * @return float
     */
    public function getHandicap(array $options): float
    {
        $this->setCalculationOptions($options);

        return $this->calculateCourseHandicap();
    }

    private function calculateCourseHandicap(): float
    {
        return round(($this->options['actualHandicap'] * ($this->options['courseSlope'] / 113)) + ($this->options['courseRating'] - $this->options['coursePar']));
    }

    /**
     * @param array $options
     * @return void
     */
    private function setCalculationOptions(array $options): void
    {
        $resolver = new OptionsResolver();

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
