<?php
namespace App\Livewire\Tools;

use Livewire\Component;

class AngleConverter extends Component
{
    public static $slug = 'angle-converter';

    public static $title = "Angle Converter - Convert Degrees, Radians, Gradians & More";

    public static $description = "Convert between all angle units including degrees, radians, gradians, turns, arcminutes, and arcseconds. Perfect for mathematics, engineering, navigation, astronomy, and trigonometry calculations.";

    public static $keywords     = 'angle converter, degrees to radians, radians to degrees, gradian converter, angle conversion, trigonometry, navigation angles';
    public static $relatedTools = ['speed-converter', 'pressure-converter', 'digital-storage-converter'];

    public string $inputValue       = '';
    public string $fromUnit         = 'degree';
    public string $toUnit           = 'radian';
    public array $results           = [];
    public array $commonConversions = [];
    public array $trigValues        = [];

    // All angle units with conversion factors to degrees (base unit)
    private function getUnits()
    {
        return [
            // Primary Units
            'degree'         => ['name' => 'Degree', 'symbol' => '°', 'to_degrees' => 1, 'category' => 'Primary', 'common' => true],
            'radian'         => ['name' => 'Radian', 'symbol' => 'rad', 'to_degrees' => 57.2957795131, 'category' => 'Primary', 'common' => true],
            'gradian'        => ['name' => 'Gradian (Gon)', 'symbol' => 'grad', 'to_degrees' => 0.9, 'category' => 'Primary', 'common' => true],
            'turn'           => ['name' => 'Turn (Revolution)', 'symbol' => 'tr', 'to_degrees' => 360, 'category' => 'Primary', 'common' => true],

            // Subdivisions
            'arcminute'      => ['name' => 'Arcminute', 'symbol' => '′', 'to_degrees' => 0.0166666667, 'category' => 'Subdivisions', 'common' => true],
            'arcsecond'      => ['name' => 'Arcsecond', 'symbol' => '″', 'to_degrees' => 0.000277777778, 'category' => 'Subdivisions', 'common' => true],
            'milliarcsecond' => ['name' => 'Milliarcsecond', 'symbol' => 'mas', 'to_degrees' => 2.77777778e-7, 'category' => 'Subdivisions', 'common' => false],
            'microarcsecond' => ['name' => 'Microarcsecond', 'symbol' => 'µas', 'to_degrees' => 2.77777778e-10, 'category' => 'Subdivisions', 'common' => false],

            // Nautical & Military
            'mil_nato'       => ['name' => 'Mil (NATO)', 'symbol' => 'mil', 'to_degrees' => 0.05625, 'category' => 'Military', 'common' => false],
            'mil_soviet'     => ['name' => 'Mil (Soviet)', 'symbol' => 'mil', 'to_degrees' => 0.05625, 'category' => 'Military', 'common' => false],
            'mil_swedish'    => ['name' => 'Streck (Swedish)', 'symbol' => 'streck', 'to_degrees' => 0.05625, 'category' => 'Military', 'common' => false],
            'point'          => ['name' => 'Point (Navigation)', 'symbol' => 'point', 'to_degrees' => 11.25, 'category' => 'Navigation', 'common' => false],

            // Circle Fractions
            'quadrant'       => ['name' => 'Quadrant', 'symbol' => 'quad', 'to_degrees' => 90, 'category' => 'Circle Fractions', 'common' => false],
            'sextant'        => ['name' => 'Sextant', 'symbol' => 'sxt', 'to_degrees' => 60, 'category' => 'Circle Fractions', 'common' => false],
            'octant'         => ['name' => 'Octant', 'symbol' => 'oct', 'to_degrees' => 45, 'category' => 'Circle Fractions', 'common' => false],
            'sign'           => ['name' => 'Sign (Zodiac)', 'symbol' => 'sign', 'to_degrees' => 30, 'category' => 'Circle Fractions', 'common' => false],

            // Binary & Computing
            'binary_degree'  => ['name' => 'Binary Degree', 'symbol' => 'brad', 'to_degrees' => 0.00140625, 'category' => 'Binary', 'common' => false],
            'minute_of_time' => ['name' => 'Minute of Time', 'symbol' => 'mot', 'to_degrees' => 0.25, 'category' => 'Time-based', 'common' => false],
            'hour_angle'     => ['name' => 'Hour Angle', 'symbol' => 'HA', 'to_degrees' => 15, 'category' => 'Time-based', 'common' => false],
        ];
    }

    public function mount()
    {
        $this->inputValue = '180';
        $this->convert();
    }

    public function updatedInputValue()
    {
        $this->convert();
    }

    public function updatedFromUnit()
    {
        $this->convert();
    }

    public function updatedToUnit()
    {
        $this->convert();
    }

    public function convert()
    {
        $this->results           = [];
        $this->commonConversions = [];
        $this->trigValues        = [];

        if (empty($this->inputValue) || ! is_numeric($this->inputValue)) {
            return;
        }

        $value = floatval($this->inputValue);
        $units = $this->getUnits();

        // Convert input to degrees first (base unit)
        $valueInDegrees = $value * $units[$this->fromUnit]['to_degrees'];

        // Normalize to 0-360 range for display
        $normalizedDegrees = fmod($valueInDegrees, 360);
        if ($normalizedDegrees < 0) {
            $normalizedDegrees += 360;
        }

        // Convert from degrees to target unit
        $result = $valueInDegrees / $units[$this->toUnit]['to_degrees'];

        $this->results = [
            'from'               => [
                'value'  => $value,
                'unit'   => $this->fromUnit,
                'name'   => $units[$this->fromUnit]['name'],
                'symbol' => $units[$this->fromUnit]['symbol'],
            ],
            'to'                 => [
                'value'     => $result,
                'unit'      => $this->toUnit,
                'name'      => $units[$this->toUnit]['name'],
                'symbol'    => $units[$this->toUnit]['symbol'],
                'formatted' => $this->formatNumber($result),
            ],
            'normalized_degrees' => $normalizedDegrees,
        ];

        // Generate common conversions
        $this->generateCommonConversions($valueInDegrees);

        // Calculate trigonometric values
        $this->calculateTrigValues($valueInDegrees);
    }

    private function generateCommonConversions($valueInDegrees)
    {
        $units = $this->getUnits();
        foreach ($units as $key => $unit) {
            if ($unit['common']) {
                $converted                 = $valueInDegrees / $unit['to_degrees'];
                $this->commonConversions[] = [
                    'unit'      => $key,
                    'name'      => $unit['name'],
                    'symbol'    => $unit['symbol'],
                    'value'     => $converted,
                    'formatted' => $this->formatNumber($converted),
                    'category'  => $unit['category'],
                ];
            }
        }
    }

    private function calculateTrigValues($degrees)
    {
        $radians = deg2rad($degrees);

        $this->trigValues = [
            'sin' => [
                'name'      => 'Sine',
                'value'     => sin($radians),
                'formatted' => $this->formatTrig(sin($radians)),
            ],
            'cos' => [
                'name'      => 'Cosine',
                'value'     => cos($radians),
                'formatted' => $this->formatTrig(cos($radians)),
            ],
            'tan' => [
                'name'      => 'Tangent',
                'value'     => abs(cos($radians)) > 1e-10 ? tan($radians) : 'undefined',
                'formatted' => abs(cos($radians)) > 1e-10 ? $this->formatTrig(tan($radians)) : 'undefined',
            ],
            'cot' => [
                'name'      => 'Cotangent',
                'value'     => abs(sin($radians)) > 1e-10 ? 1 / tan($radians) : 'undefined',
                'formatted' => abs(sin($radians)) > 1e-10 ? $this->formatTrig(1 / tan($radians)) : 'undefined',
            ],
            'sec' => [
                'name'      => 'Secant',
                'value'     => abs(cos($radians)) > 1e-10 ? 1 / cos($radians) : 'undefined',
                'formatted' => abs(cos($radians)) > 1e-10 ? $this->formatTrig(1 / cos($radians)) : 'undefined',
            ],
            'csc' => [
                'name'      => 'Cosecant',
                'value'     => abs(sin($radians)) > 1e-10 ? 1 / sin($radians) : 'undefined',
                'formatted' => abs(sin($radians)) > 1e-10 ? $this->formatTrig(1 / sin($radians)) : 'undefined',
            ],
        ];
    }

    private function formatTrig($number)
    {
        if (! is_numeric($number)) {
            return $number;
        }

        if (abs($number) < 1e-10) {
            return '0';
        }

        if (abs($number) >= 1000) {
            return number_format($number, 2, '.', ',');
        }

        return number_format($number, 6, '.', '');
    }

    private function formatNumber($number)
    {
        if (abs($number) >= 1000000) {
            return number_format($number, 2, '.', ',');
        } elseif (abs($number) >= 1000) {
            return number_format($number, 2, '.', ',');
        } elseif (abs($number) >= 1) {
            return number_format($number, 6, '.', '');
        } elseif (abs($number) >= 0.000001) {
            return number_format($number, 10, '.', '');
        } else {
            return sprintf('%.6e', $number);
        }
    }

    public function getUnitsByCategory()
    {
        $units   = $this->getUnits();
        $grouped = [];
        foreach ($units as $key => $unit) {
            $grouped[$unit['category']][$key] = $unit;
        }
        return $grouped;
    }

    public function swapUnits()
    {
        $temp           = $this->fromUnit;
        $this->fromUnit = $this->toUnit;
        $this->toUnit   = $temp;
        $this->convert();
    }

    public function render()
    {
        return view('livewire.tools.angle-converter', [
            'unitsByCategory' => $this->getUnitsByCategory(),
        ]);
    }
}
