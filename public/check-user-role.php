<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Laravel bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

try {
    // Test kullanıcısını kontrol et
    $user = \App\Models\User::with('role')->where('email', 'admin@schoolschedule.com')->first();
    
    if (!$user) {
        echo json_encode(['error' => 'Test kullanıcısı bulunamadı']);
        exit;
    }
    
    $response = [
        'user_found' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'display_name' => $user->role->display_name
            ] : null,
            'school_id' => $user->school_id
        ],
        'all_roles' => \App\Models\Role::all(),
        'schools_count' => \App\Models\School::count()
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Hata oluştu: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>