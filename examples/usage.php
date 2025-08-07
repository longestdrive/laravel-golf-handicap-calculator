<?php

use Longestdrive\LaravelGolfHandicapCalculator\LaravelGolfHandicapCalculator;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;

/**
 * Example of using the Laravel Golf Handicap Calculator with different implementations.
 */

// Example data
$actualHandicap = 15.4;
$courseSlope = 125;
$courseRating = 72.1;
$coursePar = 72;

// Method 1: Using the default implementation (WHSHandicapCalculator)
$calculator = new LaravelGolfHandicapCalculator();
$handicap = $calculator->getHandicap($actualHandicap, $courseSlope, $courseRating, $coursePar);
echo "Default (WHS) Handicap: " . $handicap . PHP_EOL;

// Method 2: Explicitly using the WHSHandicapCalculator
$whsCalculator = new LaravelGolfHandicapCalculator(new WHSHandicapCalculator());
$handicap = $whsCalculator->getHandicap($actualHandicap, $courseSlope, $courseRating, $coursePar);
echo "Explicit WHS Handicap: " . $handicap . PHP_EOL;

// Method 3: Using the SimpleHandicapCalculator
$simpleCalculator = new LaravelGolfHandicapCalculator(new SimpleHandicapCalculator());
$handicap = $simpleCalculator->getHandicap($actualHandicap, $courseSlope, $courseRating, $coursePar);
echo "Simple Handicap: " . $handicap . PHP_EOL;

// Method 4: Switching implementations at runtime
$calculator = new LaravelGolfHandicapCalculator();
echo "Initial (WHS) Handicap: " . $calculator->getHandicap($actualHandicap, $courseSlope, $courseRating, $coursePar) . PHP_EOL;

// Switch to SimpleHandicapCalculator
$calculator->setCalculator(new SimpleHandicapCalculator());
echo "After switching to Simple Handicap: " . $calculator->getHandicap($actualHandicap, $courseSlope, $courseRating, $coursePar) . PHP_EOL;

// Switch back to WHSHandicapCalculator
$calculator->setCalculator(new WHSHandicapCalculator());
echo "After switching back to WHS Handicap: " . $calculator->getHandicap($actualHandicap, $courseSlope, $courseRating, $coursePar) . PHP_EOL;

/**
 * In a Laravel application, you can also use dependency injection:
 *
 * class GolfController extends Controller
 * {
 *     protected $handicapCalculator;
 *
 *     public function __construct(LaravelGolfHandicapCalculator $handicapCalculator)
 *     {
 *         $this->handicapCalculator = $handicapCalculator;
 *     }
 *
 *     public function calculateHandicap(Request $request)
 *     {
 *         $handicap = $this->handicapCalculator->getHandicap(
 *             $request->input('actual_handicap'),
 *             $request->input('course_slope'),
 *             $request->input('course_rating'),
 *             $request->input('course_par')
 *         );
 *
 *         return response()->json(['handicap' => $handicap]);
 *     }
 * }
 */
