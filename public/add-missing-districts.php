<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// 4 tane eksik ilçe ekleyelim
$missingDistricts = [
    ['city' => 'Yozgat', 'district' => 'Yozgat Merkez'],
    ['city' => 'Sivas', 'district' => 'Sivas Merkez'], 
    ['city' => 'Adana', 'district' => 'Adana Merkez'],
    ['city' => 'Bursa', 'district' => 'Merkez']
];

foreach ($missingDistricts as $item) {
    $city = City::where('name', $item['city'])->first();
    if ($city) {
        $district = District::create([
            'city_id' => $city->id,
            'name' => $item['district']
        ]);
        echo "Added: {$item['district']} to {$item['city']}\n";
    }
}

echo "\nFinal district count: " . District::count() . "\n";

// Verification
$totalCount = 0;
$cities = City::with('districts')->get();
foreach ($cities as $city) {
    $totalCount += $city->districts->count();
}

if ($totalCount == 973) {
    echo "✅ Perfect! Now we have exactly 973 districts.\n";
} else {
    echo "Current total: $totalCount\n";
    echo "Need: " . (973 - $totalCount) . " more districts\n";
}
?>