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

    public function getHandicap(array $options): int
    {
        $this->setCalculationOptions($options);

        return (int) $this->calculateCourseHandicap();
    }

    public function reverseHandicapIndex(array $options): ?float
    {
        $this->setReverseCalculationOptions($options);

        if ($this->options['courseSlope'] == 0) {
            return null;
        }

        return ($this->options['playingHandicap'] - ($this->options['courseRating'] - $this->options['coursePar'])) * (113 / $this->options['courseSlope']);
    }

    private function calculateCourseHandicap(): float
    {
        return round(($this->options['actualHandicap'] * ($this->options['courseSlope'] / 113)) + ($this->options['courseRating'] - $this->options['coursePar']));
    }

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

    private function setReverseCalculationOptions(array $options): void
    {
        $resolver = new OptionsResolver;

        $resolver->setRequired([
            'playingHandicap',
            'courseSlope',
            'courseRating',
            'coursePar',
        ]);

        $resolver->setAllowedTypes('playingHandicap', 'int');
        $resolver->setAllowedTypes('courseSlope', ['float', 'int']);
        $resolver->setAllowedTypes('courseRating', ['float', 'int']);
        $resolver->setAllowedTypes('coursePar', 'int');

        $this->options = $resolver->resolve($options);
    }
}
