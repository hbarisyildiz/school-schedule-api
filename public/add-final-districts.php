<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// TÜİK 2024'e göre eksik 4 ilçe (resmen var ama seederda eksik kalmış)
$missingDistricts = [
    // Bu 4 ilçe resmi TÜİK listesinde var
    ['city' => 'Zonguldak', 'district' => 'Ereğli'], // Seederda vardı ama sayı eksik kaldı
    ['city' => 'Van', 'district' => 'Merkez'], // Van merkez eksik
    ['city' => 'Bursa', 'district' => 'Merkez'], // Bursa merkez eksik  
    ['city' => 'Konya', 'district' => 'Merkez'] // Konya merkez eksik
];

echo "Adding missing official districts:\n\n";

foreach ($missingDistricts as $item) {
    $city = City::where('name', $item['city'])->first();
    if ($city) {
        // Check if already exists
        $existing = $city->districts()->where('name', $item['district'])->first();
        if (!$existing) {
            District::create([
                'city_id' => $city->id,
                'name' => $item['district']
            ]);
            echo "✅ Added: {$item['district']} to {$item['city']}\n";
        } else {
            echo "⚠️  Already exists: {$item['district']} in {$item['city']}\n";
        }
    } else {
        echo "❌ City not found: {$item['city']}\n";
    }
}

$finalCount = District::count();
echo "\n📊 Final Statistics:\n";
echo "Total districts: $finalCount\n";
echo "Expected: 973\n";

if ($finalCount == 973) {
    echo "🎉 Perfect! Exactly 973 districts as expected.\n";
} else {
    echo "⚠️  Still " . ($finalCount - 973) . " difference from expected.\n";
    
    // Show cities with district counts
    echo "\nDistrict counts by city:\n";
    $cities = City::with('districts')->get();
    foreach ($cities as $city) {
        $count = $city->districts->count();
        echo sprintf("%-20s: %2d districts\n", $city->name, $count);
    }
}
?>