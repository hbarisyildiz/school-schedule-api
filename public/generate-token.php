<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔑 Yeni Token Oluşturuluyor...\n\n";

// Ortaokul için token
$ortaokul = User::where('email', 'mudur@ataturkortaokulu.edu.tr')->first();
if ($ortaokul) {
    $token = $ortaokul->createToken('admin-panel')->plainTextToken;
    echo "📋 Ortaokul Token:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $token . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

// İlkokul için token
$ilkokul = User::where('email', 'mudur@ataturkilkokulu.edu.tr')->first();
if ($ilkokul) {
    $token = $ilkokul->createToken('admin-panel')->plainTextToken;
    echo "📋 İlkokul Token:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $token . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

// Lise için token
$lise = User::where('email', 'mudur@ataturklisesi.edu.tr')->first();
if ($lise) {
    $token = $lise->createToken('admin-panel')->plainTextToken;
    echo "📋 Lise Token:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $token . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

// Super Admin için token
$superAdmin = User::where('email', 'admin@schoolschedule.com')->first();
if ($superAdmin) {
    $token = $superAdmin->createToken('admin-panel')->plainTextToken;
    echo "📋 Super Admin Token:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $token . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

echo "✅ Token'lar oluşturuldu!\n";
echo "💡 Bu token'ları fix-token.html sayfasında kullanabilirsiniz.\n";

