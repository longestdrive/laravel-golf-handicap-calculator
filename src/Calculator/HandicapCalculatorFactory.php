<?php

namespace Longestdrive\LaravelGolfHandicapCalculator\Calculator;

class HandicapCalculatorFactory
{
    protected array $calculators = [];

    public function __construct()
    {
        // Register default calculators
        $this->register('whs', WHSHandicapCalculator::class);
    }

    public function register(string $key, string $calculatorClass): void
    {
        $this->calculators[$key] = $calculatorClass;
    }

    public function make(string $key): HandicapCalculatorInterface
    {
        if (! isset($this->calculators[$key])) {
            throw new \InvalidArgumentException("Calculator [$key] not registered.");
        }

        return new $this->calculators[$key];
    }
}
