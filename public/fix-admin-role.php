<?php
header('Content-Type: text/html; charset=utf-8');

// Laravel bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

echo "<h2>Admin Kullanıcısının Rolünü Güncelleme</h2>";

try {
    // Super admin rolünü bul
    $superAdminRole = \App\Models\Role::where('name', 'super_admin')->first();
    
    if (!$superAdminRole) {
        echo "<p style='color: red;'>❌ Super admin rolü bulunamadı!</p>";
        exit;
    }
    
    echo "<p>✅ Super admin rolü bulundu (ID: {$superAdminRole->id})</p>";
    
    // Admin kullanıcısını bul
    $user = \App\Models\User::where('email', 'admin@schoolschedule.com')->first();
    
    if (!$user) {
        echo "<p style='color: red;'>❌ Admin kullanıcısı bulunamadı! Oluşturuluyor...</p>";
        
        $user = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@schoolschedule.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role_id' => $superAdminRole->id,
            'school_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        
        echo "<p>✅ Admin kullanıcısı oluşturuldu!</p>";
    } else {
        echo "<p>✅ Admin kullanıcısı bulundu (ID: {$user->id})</p>";
        
        // Rolünü güncelle
        $user->role_id = $superAdminRole->id;
        $user->school_id = null; // Super admin'in okulu olmaz
        $user->is_active = true;
        $user->save();
        
        echo "<p>✅ Admin kullanıcısının rolü super_admin olarak güncellendi!</p>";
    }
    
    // Kontrol et
    $user->load('role');
    echo "<h3>Güncel Kullanıcı Bilgileri:</h3>";
    echo "<p><strong>Ad:</strong> {$user->name}</p>";
    echo "<p><strong>Email:</strong> {$user->email}</p>";
    echo "<p><strong>Rol:</strong> " . ($user->role ? $user->role->name : 'Rol yok') . "</p>";
    echo "<p><strong>Aktif:</strong> " . ($user->is_active ? 'Evet' : 'Hayır') . "</p>";
    
    // API token testi
    echo "<h3>API Token Testi:</h3>";
    $token = $user->createToken('admin-test')->plainTextToken;
    echo "<p>✅ Token oluşturuldu: " . substr($token, 0, 20) . "...</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Hata oluştu: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>