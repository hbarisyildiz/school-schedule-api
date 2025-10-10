<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Laravel app bootstrap
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;
use App\Models\SubscriptionPlan;

echo "=== 100 Okul Ekleniyor ===\n";

// Abonelik planlarını al
$plans = SubscriptionPlan::all();

for ($i = 101; $i <= 200; $i++) {
    $plan = $plans->random();
    
    School::create([
        'name' => "Test Okulu {$i}",
        'slug' => "test-okulu-{$i}",
        'code' => "TO{$i}",
        'email' => "okul{$i}@test.edu.tr",
        'phone' => "0312 " . rand(100, 999) . " " . rand(10, 99) . " " . rand(10, 99),
        'address' => "Test Mahallesi {$i}. Sokak No:{$i}",
        'subscription_plan_id' => $plan->id,
        'subscription_starts_at' => now(),
        'subscription_ends_at' => now()->addYear(),
        'subscription_status' => 'active',
        'current_teachers' => rand(5, 50),
        'current_students' => rand(100, 1000),
        'current_classes' => rand(10, 40),
        'is_active' => true
    ]);
    
    if ($i % 10 == 0) {
        echo "✅ {$i} okul eklendi\n";
    }
}

echo "🎉 100 okul başarıyla eklendi!\n";
echo "Toplam okul sayısı: " . School::count() . "\n";
?>