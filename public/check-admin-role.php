<?php
header('Content-Type: text/html; charset=utf-8');

// Laravel bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

echo "<h2>Admin Kullanıcı Rol Kontrolü</h2>";

try {
    // Test kullanıcısını kontrol et
    $user = \App\Models\User::with('role', 'school')->where('email', 'admin@schoolschedule.com')->first();
    
    if (!$user) {
        echo "<p style='color: red;'>❌ Test kullanıcısı bulunamadı!</p>";
        
        // Tüm kullanıcıları listele
        $users = \App\Models\User::with('role')->get();
        echo "<h3>Mevcut Kullanıcılar:</h3>";
        foreach ($users as $u) {
            echo "<p>Email: {$u->email} | Rol: " . ($u->role ? $u->role->name : 'Rol yok') . "</p>";
        }
        exit;
    }
    
    echo "<h3>✅ Kullanıcı Bulundu</h3>";
    echo "<p><strong>ID:</strong> {$user->id}</p>";
    echo "<p><strong>Ad:</strong> {$user->name}</p>";
    echo "<p><strong>Email:</strong> {$user->email}</p>";
    echo "<p><strong>Rol ID:</strong> {$user->role_id}</p>";
    echo "<p><strong>School ID:</strong> {$user->school_id}</p>";
    
    if ($user->role) {
        echo "<h3>✅ Rol Bilgileri</h3>";
        echo "<p><strong>Rol ID:</strong> {$user->role->id}</p>";
        echo "<p><strong>Rol Adı:</strong> {$user->role->name}</p>";
        echo "<p><strong>Display Name:</strong> {$user->role->display_name}</p>";
        echo "<p><strong>Süper Admin mi?:</strong> " . ($user->role->name === 'super_admin' ? '✅ EVET' : '❌ HAYIR') . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Kullanıcının rolü yok!</p>";
    }
    
    // Tüm rolleri listele
    echo "<h3>Sistemdeki Tüm Roller:</h3>";
    $roles = \App\Models\Role::all();
    foreach ($roles as $role) {
        echo "<p>ID: {$role->id} | Name: {$role->name} | Display: {$role->display_name}</p>";
    }
    
    // Schools sayısı
    $schoolsCount = \App\Models\School::count();
    echo "<h3>Sistemdeki Okul Sayısı: {$schoolsCount}</h3>";
    
    // API test
    echo "<h3>API Test</h3>";
    
    // Manuel authentication yapıp schools API'yi test edelim
    \Illuminate\Support\Facades\Auth::login($user);
    
    try {
        $schools = \App\Models\School::with('subscriptionPlan')->limit(5)->get();
        echo "<p>✅ Schools API test başarılı - İlk 5 okul:</p>";
        foreach ($schools as $school) {
            echo "<p>- {$school->name} ({$school->email})</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Schools API hatası: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Hata oluştu: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>