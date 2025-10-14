<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\School;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "🔧 Kullanıcıları düzeltiyorum...\n\n";

// 1. Super Admin şifresini düzelt
$superAdmin = User::where('email', 'admin@schoolschedule.com')->first();
if ($superAdmin) {
    $superAdmin->password = Hash::make('admin123');
    $superAdmin->save();
    echo "✅ Super Admin şifresi güncellendi: admin@schoolschedule.com / admin123\n";
}

// 2. İlkokul oluştur
$ilkokul = School::firstOrCreate(
    ['name' => 'Atatürk İlkokulu'],
    [
        'slug' => 'ataturk-ilkokulu',
        'email' => 'mudur@ataturkilkokulu.edu.tr',
        'phone' => '02121234567',
        'website' => 'https://ataturkilkokulu.edu.tr',
        'school_type' => 'ilkokul',
        'subscription_plan_id' => 1,
        'subscription_starts_at' => now(),
        'subscription_ends_at' => now()->addYear(),
        'subscription_status' => 'active',
        'is_active' => true
    ]
);
echo "✅ İlkokul oluşturuldu: {$ilkokul->name}\n";

// İlkokul müdürü oluştur
$ilkokulMuduru = User::firstOrCreate(
    ['email' => 'mudur@ataturkilkokulu.edu.tr'],
    [
        'name' => 'Ayşe Yılmaz',
        'password' => Hash::make('mudur123'),
        'role_id' => 2, // school_admin
        'school_id' => $ilkokul->id,
        'is_active' => true
    ]
);
echo "✅ İlkokul Müdürü oluşturuldu: mudur@ataturkilkokulu.edu.tr / mudur123\n";

// 3. Ortaokul oluştur
$ortaokul = School::firstOrCreate(
    ['name' => 'Atatürk Ortaokulu'],
    [
        'slug' => 'ataturk-ortaokulu',
        'email' => 'mudur@ataturkortaokulu.edu.tr',
        'phone' => '02121234568',
        'website' => 'https://ataturkortaokulu.edu.tr',
        'school_type' => 'ortaokul',
        'subscription_plan_id' => 1,
        'subscription_starts_at' => now(),
        'subscription_ends_at' => now()->addYear(),
        'subscription_status' => 'active',
        'is_active' => true
    ]
);
echo "✅ Ortaokul oluşturuldu: {$ortaokul->name}\n";

// Ortaokul müdürü oluştur
$ortaokulMuduru = User::firstOrCreate(
    ['email' => 'mudur@ataturkortaokulu.edu.tr'],
    [
        'name' => 'Mehmet Demir',
        'password' => Hash::make('mudur123'),
        'role_id' => 2, // school_admin
        'school_id' => $ortaokul->id,
        'is_active' => true
    ]
);
echo "✅ Ortaokul Müdürü oluşturuldu: mudur@ataturkortaokulu.edu.tr / mudur123\n";

echo "\n🎉 Tamamlandı!\n\n";
echo "📋 Giriş Bilgileri:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "👑 Super Admin:\n";
echo "   Email: admin@schoolschedule.com\n";
echo "   Şifre: admin123\n\n";
echo "🏫 İlkokul:\n";
echo "   Email: mudur@ataturkilkokulu.edu.tr\n";
echo "   Şifre: mudur123\n\n";
echo "🏫 Ortaokul:\n";
echo "   Email: mudur@ataturkortaokulu.edu.tr\n";
echo "   Şifre: mudur123\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

