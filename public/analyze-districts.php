<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

echo "Analyzing district counts by city:\n\n";

$cities = City::with('districts')->get();
$totalDistricts = 0;

foreach ($cities as $city) {
    $districtCount = $city->districts->count();
    $totalDistricts += $districtCount;
    echo sprintf("%-20s: %2d districts\n", $city->name, $districtCount);
}

echo "\n" . str_repeat("-", 40) . "\n";
echo "Total districts: $totalDistricts\n";
echo "Expected districts: 973\n";
echo "Difference: " . ($totalDistricts - 973) . "\n\n";

// Check for potential duplicates
echo "Checking for duplicate district names:\n";
$duplicates = District::select('name', 'city_id')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('name', 'city_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "Found duplicates:\n";
    foreach ($duplicates as $dup) {
        echo "- {$dup->name} (city_id: {$dup->city_id}): {$dup->count} times\n";
    }
} else {
    echo "No duplicates found.\n";
}
?>