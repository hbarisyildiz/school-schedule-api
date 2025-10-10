<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Laravel app bootstrap
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Veritabanındaki Kullanıcılar ===\n\n";

try {
    $users = User::with(['role', 'school'])->get();
    
    if ($users->isEmpty()) {
        echo "❌ Hiç kullanıcı bulunamadı!\n";
    } else {
        foreach ($users as $user) {
            echo "ID: {$user->id}\n";
            echo "Ad: {$user->name}\n";
            echo "Email: {$user->email}\n";
            echo "Rol: {$user->role->name} ({$user->role->display_name})\n";
            echo "Okul: " . ($user->school ? $user->school->name : 'Yok') . "\n";
            echo "Aktif: " . ($user->is_active ? 'Evet' : 'Hayır') . "\n";
            echo "Şifre Hash: " . substr($user->password, 0, 20) . "...\n";
            echo "---\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
?>