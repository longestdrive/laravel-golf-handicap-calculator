# Changelog

All notable changes to `laravel-golf-handicap-calculator` will be documented in this file.

## v2.0.0 - Reverse Handicap Index - 2026-03-25

### Breaking Changes

- `HandicapCalculatorInterface` now requires a `reverseHandicapIndex(array $options): ?float` method. Custom calculator implementations must add this method.

### New Features

- Added `reverseHandicapIndex()` to `WHSHandicapCalculator` and `SimpleHandicapCalculator`
- Reverse-calculates an estimated handicap index from a known playing handicap, course slope, course rating, and course par
- Returns `null` when `courseSlope` is zero to prevent division by zero
- Full test coverage across all calculators and feature tests

## v2.0.0 - 2026-03-25

### Breaking Changes

- `HandicapCalculatorInterface` now requires a `reverseHandicapIndex(array $options): ?float` method. Any custom calculator implementations must add this method.

### New Features

- Added `reverseHandicapIndex()` to `WHSHandicapCalculator` — reverse-calculates an estimated handicap index from a playing handicap using the WHS formula: `(playingHandicap - (courseRating - coursePar)) × (113 / courseSlope)`
- Added `reverseHandicapIndex()` to `SimpleHandicapCalculator` — reverse-calculates using the simplified formula: `(playingHandicap - (courseRating - coursePar) / 2) × (100 / courseSlope)`
- Returns `null` when `courseSlope` is zero to avoid division by zero
- Full test coverage for the new method across all calculators and feature tests

## Fixes to tests and handicap calculation - 2025-08-08

Fixed getHandicap return to integer instead of float

## First release of calculator - 2025-08-08

First release with WHS rules and simple rules
