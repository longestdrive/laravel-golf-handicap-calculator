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

        $slope = $this->options['courseSlope'];
        $courseRating = $this->options['courseRating'];
        $coursePar = $this->options['coursePar'];
        $playingHandicap = $this->options['playingHandicap'];

        if ($slope == 0) {
            return null;
        }

        $raw = ($playingHandicap - ($courseRating - $coursePar)) * (113 / $slope);
        $start = (int) round($raw * 10);

        $best = null;
        $bestDistance = null;

        for ($delta = -100; $delta <= 100; $delta++) {
            $candidate = ($start + $delta) / 10;

            if ($candidate < -5 || $candidate > 54) {
                continue;
            }

            $recalculated = $this->getHandicap([
                'actualHandicap' => $candidate,
                'courseSlope' => $slope,
                'courseRating' => $courseRating,
                'coursePar' => $coursePar,
            ]);

            if ($recalculated !== $playingHandicap) {
                continue;
            }

            $distance = abs($candidate - $raw);
            if ($bestDistance === null || $distance < $bestDistance) {
                $best = $candidate;
                $bestDistance = $distance;
            }
        }

        return $best !== null ? (float) $best : null;
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
