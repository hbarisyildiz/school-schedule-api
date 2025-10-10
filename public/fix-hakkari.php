<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// Hakkâri'yi bul
$city = City::where('name', 'like', '%hakk%')->first();
if ($city) {
    echo "Found city: " . $city->name . " (ID: " . $city->id . ")\n";
    
    // İlçeleri ekle
    $districts = ['Çukurca', 'Hakkâri Merkez', 'Şemdinli', 'Yüksekova'];
    foreach ($districts as $district) {
        District::updateOrCreate([
            'city_id' => $city->id,
            'name' => $district
        ]);
        echo "Added: $district\n";
    }
} else {
    echo "Hakkâri not found. Available cities with 'Hakk':\n";
    City::where('name', 'like', '%hakk%')->get()->each(function($c) {
        echo $c->name . "\n";
    });
}

// Toplam sayıları göster
echo "\nTotal cities: " . City::count() . "\n";
echo "Total districts: " . District::count() . "\n";
?>