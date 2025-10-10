<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// Tekirdağ'ı kontrol et
$tekirdag = City::where('name', 'Tekirdağ')->first();
if ($tekirdag) {
    echo "Tekirdağ districts (" . $tekirdag->districts->count() . "):\n";
    foreach ($tekirdag->districts as $district) {
        echo "- " . $district->name . "\n";
    }
    
    // Şarköy var mı?
    $sarköy = $tekirdag->districts()->where('name', 'Şarköy')->first();
    if ($sarköy) {
        echo "\n✅ Şarköy found!\n";
    } else {
        echo "\n❌ Şarköy not found!\n";
    }
}

echo "\nTotal districts: " . District::count() . "\n";
echo "Expected: 973\n";
echo "Missing: " . (973 - District::count()) . "\n";

// Check some other cities for verification
$cities = ['Bursa', 'Ankara', 'İstanbul'];
foreach ($cities as $cityName) {
    $city = City::where('name', $cityName)->first();
    if ($city) {
        echo "$cityName: " . $city->districts->count() . " districts\n";
    }
}
?>