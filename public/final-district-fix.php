<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// Yozgat'tan 1 ilçe sil (15->14)
$yozgat = City::where('name', 'Yözgat')->first();
if ($yozgat && $yozgat->districts->count() > 14) {
    $lastDistrict = $yozgat->districts()->orderBy('id', 'desc')->first();
    echo "Removing: {$lastDistrict->name} from Yözgat\n";
    $lastDistrict->delete();
}

echo "Final count: " . District::count() . " districts\n";

// İstatistikleri göster
$cities = City::with('districts')->get();
$totalDistricts = 0;
foreach ($cities as $city) {
    $totalDistricts += $city->districts->count();
}

echo "Verification: $totalDistricts districts total\n";

if ($totalDistricts == 973) {
    echo "✅ Perfect! We now have exactly 973 districts as expected.\n";
} else {
    echo "❌ Still have " . ($totalDistricts - 973) . " extra districts.\n";
}
?>