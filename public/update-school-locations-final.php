<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;
use App\Models\City;
use App\Models\District;

// Get all cities with their districts
$citiesWithDistricts = City::with('districts')->get();

// Get all schools without city assignment
$schools = School::whereNull('city_id')->orWhereNull('district_id')->get();

echo "Found " . $schools->count() . " schools to update\n";

foreach ($schools as $school) {
    // Pick a random city
    $randomCity = $citiesWithDistricts->random();
    
    // Pick a random district from that city
    if ($randomCity->districts->count() > 0) {
        $randomDistrict = $randomCity->districts->random();
        
        $school->update([
            'city_id' => $randomCity->id,
            'district_id' => $randomDistrict->id
        ]);
        
        echo "Updated {$school->name} -> {$randomCity->name} / {$randomDistrict->name}\n";
    } else {
        echo "No districts found for {$randomCity->name}\n";
    }
}

echo "\nCompleted! All schools now have location data.\n";
echo "Total schools: " . School::count() . "\n";
echo "Schools with location: " . School::whereNotNull('city_id')->whereNotNull('district_id')->count() . "\n";
?>