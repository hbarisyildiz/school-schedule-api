<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Laravel bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

try {
    // Test kullanıcısını authenticate et
    $user = \App\Models\User::where('email', 'admin@schoolschedule.com')->first();
    
    if (!$user) {
        echo json_encode(['error' => 'Test kullanıcısı bulunamadı']);
        exit;
    }
    
    // Manuel authentication
    \Illuminate\Support\Facades\Auth::login($user);
    
    // Schools'u çek
    $schools = \App\Models\School::with('subscriptionPlan')->get();
    
    $response = [
        'success' => true,
        'message' => 'Schools başarıyla yüklendi',
        'data' => $schools,
        'count' => $schools->count(),
        'user' => [
            'name' => $user->name,
            'role' => $user->role->name
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Hata oluştu: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>