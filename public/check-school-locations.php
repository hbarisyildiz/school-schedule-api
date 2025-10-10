<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;
use App\Models\City;
use App\Models\District;

// Orphaned schools check (districts that no longer exist)
$schoolsWithInvalidDistricts = School::whereNotNull('district_id')
    ->whereNotExists(function($query) {
        $query->select('id')
              ->from('districts')
              ->whereRaw('districts.id = schools.district_id');
    })->get();

if ($schoolsWithInvalidDistricts->count() > 0) {
    echo "Found " . $schoolsWithInvalidDistricts->count() . " schools with invalid district references.\n";
    echo "Fixing them...\n\n";
    
    $citiesWithDistricts = City::with('districts')->get();
    
    foreach ($schoolsWithInvalidDistricts as $school) {
        // Get a random city and district
        $randomCity = $citiesWithDistricts->random();
        $randomDistrict = $randomCity->districts->random();
        
        $school->update([
            'city_id' => $randomCity->id,
            'district_id' => $randomDistrict->id
        ]);
        
        echo "Fixed: {$school->name} -> {$randomCity->name} / {$randomDistrict->name}\n";
    }
    
    echo "\nAll schools fixed!\n";
} else {
    echo "✅ All schools have valid location references.\n";
}

// Final statistics
echo "\n📊 Final Statistics:\n";
echo "Cities: " . City::count() . "\n";
echo "Districts: " . District::count() . "\n";
echo "Schools: " . School::count() . "\n";
echo "Schools with location: " . School::whereNotNull('city_id')->whereNotNull('district_id')->count() . "\n";
?>