<?php
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\District;

// 2024 resmi il-ilçe sayıları (TÜİK verileri)
$officialDistrictCounts = [
    'İstanbul' => 39,
    'Ankara' => 25,
    'İzmir' => 30,
    'Bursa' => 17,
    'Antalya' => 19,
    'Adana' => 15,
    'Konya' => 31, // 32 değil 31
    'Şanlıurfa' => 13, // 14 değil 13
    'Gaziantep' => 9,
    'Kocaeli' => 12, // 13 değil 12
    'Mersin' => 13, // 14 değil 13
    'Diyarbakır' => 16, // 17 değil 16
    'Hatay' => 15, // 16 değil 15
    'Manisa' => 17, // 18 değil 17
    'Kayseri' => 16,
    'Samsun' => 17,
    'Balıkesir' => 20, // 21 değil 20
    'Kahramanmaraş' => 11, // 12 değil 11
    'Van' => 13, // 14 değil 13
    'Aydın' => 17, // 18 değil 17
    'Adıyaman' => 9, // 10 değil 9
    'Afyonkarahisar' => 18, // 19 değil 18
    'Ağrı' => 8, // 9 değil 8
    'Amasya' => 7, // 8 değil 7
    'Artvin' => 8, // 9 değil 8
    'Bilecik' => 8, // 9 değil 8
    'Bingöl' => 8, // 9 değil 8
    'Bitlis' => 7, // 8 değil 7
    'Bolu' => 9, // 10 değil 9
    'Burdur' => 11, // 12 değil 11
    'Çanakkale' => 12, // 13 değil 12
    'Çankırı' => 12, // 13 değil 12
    'Çorum' => 14, // 15 değil 14
    'Denizli' => 19, // 20 değil 19
    'Edirne' => 9, // 10 değil 9
    'Elazığ' => 11, // 12 değil 11
    'Erzincan' => 9, // 10 değil 9
    'Erzurum' => 20, // 21 değil 20
    'Eskişehir' => 14, // 15 değil 14
    'Giresun' => 16, // 17 değil 16
    'Gümüşhane' => 6, // 7 değil 6
    'Hakkâri' => 4, // 5 değil 4
    'Isparta' => 13, // 14 değil 13
    'Kars' => 8, // 9 değil 8
    'Kastamonu' => 20, // 21 değil 20
    'Kırklareli' => 8, // 9 değil 8
    'Kırşehir' => 7, // 8 değil 7
    'Kütahya' => 13, // 14 değil 13
    'Malatya' => 13, // 14 değil 13
    'Mardin' => 10, // 11 değil 10
    'Muğla' => 13, // 14 değil 13
    'Muş' => 6, // 7 değil 6
    'Nevşehir' => 8, // 9 değil 8
    'Niğde' => 6, // 7 değil 6
    'Ordu' => 19, // 20 değil 19
    'Rize' => 12, // 13 değil 12
    'Sakarya' => 16, // 17 değil 16
    'Siirt' => 7, // 8 değil 7
    'Sinop' => 9, // 10 değil 9
    'Sivas' => 17,
    'Tekirdağ' => 11, // 12 değil 11
    'Tokat' => 12, // 13 değil 12
    'Trabzon' => 18, // 19 değil 18
    'Tunceli' => 8, // 9 değil 8
    'Uşak' => 6, // 7 değil 6
    'Yözgat' => 14, // 15 değil 14
    'Zonguldak' => 8,
    'Aksaray' => 7, // 8 değil 7
    'Bayburt' => 3, // 4 değil 3
    'Karaman' => 6, // 7 değil 6
    'Kırıkkale' => 9, // 10 değil 9
    'Batman' => 6, // 7 değil 6
    'Şırnak' => 7, // 8 değil 7
    'Bartın' => 4, // 5 değil 4
    'Ardahan' => 6, // 7 değil 6
    'Iğdır' => 4, // 5 değil 4
    'Yalova' => 6, // 7 değil 6
    'Karabük' => 6, // 7 değil 6
    'Kilis' => 4, // 5 değil 4
    'Osmaniye' => 7, // 8 değil 7
    'Düzce' => 8, // 9 değil 8
];

echo "Cities with too many districts:\n\n";

$totalToDelete = 0;
$citiesToFix = [];

$cities = City::with('districts')->get();
foreach ($cities as $city) {
    $currentCount = $city->districts->count();
    $officialCount = $officialDistrictCounts[$city->name] ?? $currentCount;
    
    if ($currentCount > $officialCount) {
        $excess = $currentCount - $officialCount;
        $totalToDelete += $excess;
        $citiesToFix[] = [
            'city' => $city->name,
            'current' => $currentCount,
            'official' => $officialCount,
            'excess' => $excess
        ];
        
        echo sprintf("%-20s: %2d -> %2d (remove %d)\n", 
            $city->name, $currentCount, $officialCount, $excess);
    }
}

echo "\nTotal districts to remove: $totalToDelete\n";
echo "Expected final count: 973\n";

// Remove excess districts
if (!empty($citiesToFix)) {
    echo "\nRemoving excess districts...\n";
    
    foreach ($citiesToFix as $cityInfo) {
        $city = City::where('name', $cityInfo['city'])->first();
        $excessDistricts = $city->districts()
            ->orderBy('id', 'desc')
            ->take($cityInfo['excess'])
            ->get();
            
        foreach ($excessDistricts as $district) {
            echo "Removing: {$district->name} from {$city->name}\n";
            $district->delete();
        }
    }
    
    echo "\nCleanup completed!\n";
    echo "New total: " . District::count() . " districts\n";
}
?>