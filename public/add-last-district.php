<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// Son eksik 1 ilçeyi ekle
$sivas = City::where('name', 'Sivas')->first();
if ($sivas && !$sivas->districts()->where('name', 'Merkez')->exists()) {
    District::create([
        'city_id' => $sivas->id,
        'name' => 'Merkez'
    ]);
    echo "Added: Sivas Merkez\n";
} else {
    echo "Sivas Merkez already exists\n";
}

echo "Final count: " . District::count() . "\n";

if (District::count() == 973) {
    echo "🎉 Perfect! Exactly 973 districts.\n";
}
?>