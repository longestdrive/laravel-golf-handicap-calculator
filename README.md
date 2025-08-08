# calculates golf handicaps using various methods including WHS rules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/longestdrive/laravel-golf-handicap-calculator.svg?style=flat-square)](https://packagist.org/packages/longestdrive/laravel-golf-handicap-calculator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/longestdrive/laravel-golf-handicap-calculator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/longestdrive/laravel-golf-handicap-calculator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/longestdrive/laravel-golf-handicap-calculator/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/longestdrive/laravel-golf-handicap-calculator/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/longestdrive/laravel-golf-handicap-calculator.svg?style=flat-square)](https://packagist.org/packages/longestdrive/laravel-golf-handicap-calculator)

A Laravel package for calculating golf handicaps using various methods, including the World Handicap System (WHS) rules. This package provides a flexible factory-based approach that allows you to:

- Use the built-in WHS handicap calculator
- Register and use the included SimpleHandicapCalculator
- Create and register your own custom handicap calculators
- Switch between different calculation methods at runtime

The package uses a factory pattern that makes it easy to extend with your own calculation methods while maintaining a consistent interface.

## Support us
Built using [spatie/laravel-package-skeleton]

## Installation

You can install the package via composer:

```bash
composer require longestdrive/laravel-golf-handicap-calculator
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-golf-handicap-calculator-config"
```
WIP
This is the contents of the published config file:

```php
return [
];
```


## Usage

### Basic Usage

#### Using the Factory Approach (Recommended)

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;

// Create a calculator instance using the factory (uses WHSHandicapCalculator by default)
$calculator = GolfHandicapCalculator::make('whs');

// Calculate a handicap using the options array
$handicap = $calculator->getHandicap([
    'actualHandicap' => 15.4,  // Player's actual handicap (float)
    'courseSlope' => 125,      // Course slope rating (int)
    'courseRating' => 72.1,    // Course rating (float)
    'coursePar' => 72          // Course par (int)
]);

echo "Playing Handicap: " . $handicap;
```

#### Legacy Approach

```php
use Longestdrive\LaravelGolfHandicapCalculator\LaravelGolfHandicapCalculator;

// Create a calculator instance (uses WHSHandicapCalculator by default)
$calculator = new LaravelGolfHandicapCalculator();

// Calculate a handicap using the options array
$handicap = $calculator->getHandicap([
    'actualHandicap' => 15.4,  // Player's actual handicap (float)
    'courseSlope' => 125,      // Course slope rating (int)
    'courseRating' => 72.1,    // Course rating (float)
    'coursePar' => 72          // Course par (int)
]);

echo "Playing Handicap: " . $handicap;
```

### Using Different Calculation Methods

The package is designed with a flexible factory approach that allows you to implement and use different handicap calculation strategies:

#### Using the Factory Approach (Recommended)

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;
use App\Services\CustomHandicapCalculator; // Your custom implementation

// Register your custom calculator
GolfHandicapCalculator::register('custom', CustomHandicapCalculator::class);

// Create a calculator instance using the factory
$whsCalculator = GolfHandicapCalculator::make('whs'); // Default WHS calculator
$customCalculator = GolfHandicapCalculator::make('custom'); // Your custom calculator

// Calculate handicap using the selected calculator
$options = [
    'actualHandicap' => 15.4,
    'courseSlope' => 125,
    'courseRating' => 72.1,
    'coursePar' => 72
];

$handicap = $whsCalculator->getHandicap($options);
$customHandicap = $customCalculator->getHandicap($options);
```

#### Available Default Calculators

The package comes with the following built-in calculator:

1. `whs` - World Handicap System calculator (default)

The package also includes a `SimpleHandicapCalculator` class that you can register manually:

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\SimpleHandicapCalculator;

// Register the SimpleHandicapCalculator
GolfHandicapCalculator::register('simple', SimpleHandicapCalculator::class);

// Now you can use it
$simpleCalculator = GolfHandicapCalculator::make('simple');
$handicap = $simpleCalculator->getHandicap($options);
```

#### Legacy Approach

You can also use the direct instantiation approach:

```php
use Longestdrive\LaravelGolfHandicapCalculator\LaravelGolfHandicapCalculator;
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\WHSHandicapCalculator;
use App\Services\CustomHandicapCalculator; // Your custom implementation

// Using the default WHS calculator
$calculator = new LaravelGolfHandicapCalculator();

// Explicitly using the WHS calculator
$calculator = new LaravelGolfHandicapCalculator(new WHSHandicapCalculator());

// Using your custom calculator
$calculator = new LaravelGolfHandicapCalculator(new CustomHandicapCalculator());

// Switching calculators at runtime
$calculator = new LaravelGolfHandicapCalculator();
$calculator->setCalculator(new CustomHandicapCalculator());

// All calculators use the same options array format
$options = [
    'actualHandicap' => 15.4,
    'courseSlope' => 125,
    'courseRating' => 72.1,
    'coursePar' => 72
];

$handicap = $calculator->getHandicap($options);
```

### Creating Your Own Calculator

You can create your own handicap calculator by implementing the `HandicapCalculatorInterface` and registering it with the factory:

#### 1. Create Your Calculator Class

```php
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomHandicapCalculator implements HandicapCalculatorInterface
{
    private array $options;
    
    public function getHandicap(array $options): float
    {
        // Validate and set options
        $this->setCalculationOptions($options);
        
        // Your custom calculation logic here
        return round(/* your formula using $this->options */);
    }
    
    private function setCalculationOptions(array $options): void
    {
        $resolver = new OptionsResolver();
        
        // Define required options
        $resolver->setRequired([
            'actualHandicap',
            'courseSlope',
            'courseRating',
            'coursePar',
            // Add any additional options your calculator needs
        ]);
        
        // Define allowed types for each option
        $resolver->setAllowedTypes('actualHandicap', ['float', 'int']);
        $resolver->setAllowedTypes('courseSlope', ['float', 'int']);
        $resolver->setAllowedTypes('courseRating', ['float', 'int']);
        $resolver->setAllowedTypes('coursePar', 'int');
        
        $this->options = $resolver->resolve($options);
    }
}
```

#### 2. Register Your Calculator with the Factory

You can register your calculator in a service provider:

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;
use App\Services\CustomHandicapCalculator;

public function boot()
{
    // Register your custom calculator
    GolfHandicapCalculator::register('custom', CustomHandicapCalculator::class);
}
```

Or register it directly in your application code:

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;
use App\Services\CustomHandicapCalculator;

// Register your custom calculator
GolfHandicapCalculator::register('custom', CustomHandicapCalculator::class);
```

#### 3. Use Your Custom Calculator

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;

// Create an instance of your custom calculator
$calculator = GolfHandicapCalculator::make('custom');

// Use it to calculate a handicap
$handicap = $calculator->getHandicap([
    'actualHandicap' => 15.4,
    'courseSlope' => 125,
    'courseRating' => 72.1,
    'coursePar' => 72
]);
```

### Laravel Service Container Integration

The package registers the HandicapCalculatorFactory in Laravel's service container, allowing you to use dependency injection:

#### Using the Factory in Controllers

```php
use Longestdrive\LaravelGolfHandicapCalculator\Calculator\HandicapCalculatorFactory;
use Illuminate\Http\Request;

class GolfController extends Controller
{
    protected $calculatorFactory;
    
    public function __construct(HandicapCalculatorFactory $calculatorFactory)
    {
        $this->calculatorFactory = $calculatorFactory;
    }
    
    public function calculateHandicap(Request $request, string $calculatorType = 'whs')
    {
        // Get the requested calculator type or default to 'whs'
        $calculator = $this->calculatorFactory->make($calculatorType);
        
        // Create options array from request inputs
        $options = [
            'actualHandicap' => (float) $request->input('actual_handicap'),
            'courseSlope' => (int) $request->input('course_slope'),
            'courseRating' => (float) $request->input('course_rating'),
            'coursePar' => (int) $request->input('course_par')
        ];
        
        // Pass the options array to the calculator
        $handicap = $calculator->getHandicap($options);
        
        return response()->json(['handicap' => $handicap]);
    }
}
```

#### Using the Facade in Controllers

```php
use Longestdrive\LaravelGolfHandicapCalculator\Facades\GolfHandicapCalculator;
use Illuminate\Http\Request;

class GolfController extends Controller
{
    public function calculateHandicap(Request $request, string $calculatorType = 'whs')
    {
        // Get the requested calculator type or default to 'whs'
        $calculator = GolfHandicapCalculator::make($calculatorType);
        
        // Create options array from request inputs
        $options = [
            'actualHandicap' => (float) $request->input('actual_handicap'),
            'courseSlope' => (int) $request->input('course_slope'),
            'courseRating' => (float) $request->input('course_rating'),
            'coursePar' => (int) $request->input('course_par')
        ];
        
        // Pass the options array to the calculator
        $handicap = $calculator->getHandicap($options);
        
        return response()->json(['handicap' => $handicap]);
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Longestdrive](https://github.com/Longestdrive)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
