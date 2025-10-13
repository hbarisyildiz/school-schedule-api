<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating 100 Random Schools ===\n\n";

// Okul isimleri için kelimeler
$schoolTypes = [
    'Anadolu Lisesi',
    'Fen Lisesi',
    'İmam Hatip Lisesi',
    'Meslek Lisesi',
    'Teknik Lise',
    'Güzel Sanatlar Lisesi',
    'Spor Lisesi',
    'Bilim ve Sanat Merkezi',
    'Ortaokulu',
    'İlkokulu'
];

$schoolPrefixes = [
    'Atatürk',
    'Cumhuriyet',
    'Fatih',
    'Mehmet Akif',
    'Gazi',
    'Mimar Sinan',
    'İstiklal',
    'Zafer',
    'Kurtuluş',
    'Atatürk',
    'Yunus Emre',
    'Mevlana',
    'Barbaros',
    'Kanuni',
    'Fatih Sultan Mehmet',
    'Alparslan',
    'Merkez',
    'Şehit',
    'Gaziler',
    'Anadolu'
];

$maleNames = ['Ahmet', 'Mehmet', 'Ali', 'Mustafa', 'Hasan', 'Hüseyin', 'İbrahim', 'Ömer', 'Yusuf', 'Emre', 'Burak', 'Cem', 'Deniz', 'Eren', 'Fatih', 'Kemal', 'Murat', 'Onur', 'Serkan', 'Tolga'];
$femaleNames = ['Ayşe', 'Fatma', 'Elif', 'Zeynep', 'Merve', 'Emine', 'Hatice', 'Selin', 'Esra', 'Beyza', 'Büşra', 'Gamze', 'İrem', 'Kübra', 'Melike', 'Pınar', 'Rabia', 'Sibel', 'Tuğba', 'Yasemin'];
$lastNames = ['Yılmaz', 'Kaya', 'Demir', 'Şahin', 'Çelik', 'Öztürk', 'Aydın', 'Özdemir', 'Arslan', 'Doğan', 'Kılıç', 'Aslan', 'Çetin', 'Koç', 'Kurt', 'Özkan', 'Şimşek', 'Yıldız', 'Yıldırım', 'Özer'];

// Tüm illeri çek
$cities = \App\Models\City::all();
if ($cities->isEmpty()) {
    echo "❌ No cities found in database! Please seed cities first.\n";
    exit(1);
}

// Subscription planlarını çek
$plans = \App\Models\SubscriptionPlan::all();
if ($plans->isEmpty()) {
    echo "❌ No subscription plans found! Please seed plans first.\n";
    exit(1);
}

// Rolleri çek
$schoolAdminRole = \App\Models\Role::where('name', 'school_admin')->first();
if (!$schoolAdminRole) {
    echo "❌ School admin role not found!\n";
    exit(1);
}

$created = 0;
$skipped = 0;

for ($i = 1; $i <= 100; $i++) {
    // Rastgele il seç
    $city = $cities->random();
    
    // O ilin ilçelerini çek
    $districts = \App\Models\District::where('city_id', $city->id)->get();
    if ($districts->isEmpty()) {
        echo "⚠️ No districts found for {$city->name}, skipping...\n";
        $skipped++;
        continue;
    }
    $district = $districts->random();
    
    // Rastgele okul adı oluştur
    $prefix = $schoolPrefixes[array_rand($schoolPrefixes)];
    $type = $schoolTypes[array_rand($schoolTypes)];
    $schoolName = "{$prefix} {$type}";
    
    // Slug oluştur
    $slug = \Illuminate\Support\Str::slug($schoolName . '-' . $district->name . '-' . $i);
    
    // Okul kodu oluştur
    $cityCode = strtoupper(substr($city->name, 0, 2));
    $schoolCode = $cityCode . sprintf('%04d', $i);
    
    // Email oluştur
    $emailPrefix = \Illuminate\Support\Str::slug($prefix);
    $email = "info@{$emailPrefix}{$i}.edu.tr";
    
    // Telefon oluştur
    $phone = '0' . rand(300, 599) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99);
    
    // Rastgele plan seç (ağırlıklı: %60 Basic, %30 Standard, %10 Premium)
    $rand = rand(1, 100);
    if ($rand <= 60) {
        $plan = $plans->where('slug', 'basic')->first();
    } elseif ($rand <= 90) {
        $plan = $plans->where('slug', 'standard')->first();
    } else {
        $plan = $plans->where('slug', 'premium')->first();
    }
    
    if (!$plan) {
        $plan = $plans->first();
    }
    
    // Subscription süresi
    $subscriptionMonths = rand(1, 12);
    $subscriptionEnds = now()->addMonths($subscriptionMonths);
    
    // Okul durumu (95% aktif)
    $isActive = rand(1, 100) <= 95;
    
    // Okulun olup olmadığını kontrol et
    $existingSchool = \App\Models\School::where('email', $email)->first();
    if ($existingSchool) {
        $skipped++;
        continue;
    }
    
    try {
        // Okul oluştur
        $school = \App\Models\School::create([
            'name' => $schoolName,
            'slug' => $slug,
            'code' => $schoolCode,
            'email' => $email,
            'phone' => $phone,
            'address' => "{$district->name}, {$city->name}",
            'city_id' => $city->id,
            'district_id' => $district->id,
            'subscription_plan_id' => $plan->id,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => $subscriptionEnds,
            'subscription_status' => 'active',
            'current_teachers' => rand(10, 50),
            'current_students' => rand(100, 800),
            'current_classes' => rand(8, 24),
            'is_active' => $isActive
        ]);
        
        // Okul yöneticisi oluştur
        $isFemale = rand(0, 1);
        $firstName = $isFemale ? $femaleNames[array_rand($femaleNames)] : $maleNames[array_rand($maleNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $adminName = "{$firstName} {$lastName}";
        
        $adminEmail = "mudur@{$emailPrefix}{$i}.edu.tr";
        $adminPhone = '0' . rand(530, 559) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99);
        
        \App\Models\User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => bcrypt('123456'), // Varsayılan şifre
            'school_id' => $school->id,
            'role_id' => $schoolAdminRole->id,
            'phone' => $adminPhone,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        
        $created++;
        
        // Her 10 okulda bir progress göster
        if ($created % 10 === 0) {
            echo "✅ {$created} okul oluşturuldu...\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error creating school {$i}: {$e->getMessage()}\n";
        $skipped++;
    }
}

echo "\n=== Summary ===\n";
echo "✅ Successfully created: {$created} schools\n";
echo "⚠️ Skipped: {$skipped} schools\n";
echo "📊 Total attempts: 100\n\n";

echo "=== Random School Examples ===\n";
$samples = \App\Models\School::with(['city', 'district', 'subscriptionPlan'])
    ->inRandomOrder()
    ->limit(5)
    ->get();

foreach ($samples as $sample) {
    echo "\n📍 {$sample->name}\n";
    echo "   Location: {$sample->district->name}, {$sample->city->name}\n";
    echo "   Email: {$sample->email}\n";
    echo "   Plan: {$sample->subscriptionPlan->name}\n";
    echo "   Status: " . ($sample->is_active ? 'Active' : 'Inactive') . "\n";
    
    // Yöneticisini bul
    $admin = \App\Models\User::where('school_id', $sample->id)
        ->where('role_id', $schoolAdminRole->id)
        ->first();
    if ($admin) {
        echo "   Admin: {$admin->name} ({$admin->email})\n";
        echo "   Admin Password: 123456\n";
    }
}

echo "\n=== All schools are ready! ===\n";
echo "Default admin password for all schools: 123456\n";
echo "View them at: http://localhost/admin-panel-modern.html\n";
?>


