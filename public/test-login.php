<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    // Test kullanıcıları
    $testUsers = [
        'super_admin' => [
            'email' => 'admin@schoolschedule.com',
            'password' => 'admin123',
            'role' => 'Super Admin'
        ],
        'school_admin' => [
            'email' => 'mudur@ataturklisesi.edu.tr', 
            'password' => 'mudur123',
            'role' => 'Okul Yöneticisi'
        ],
        'teacher' => [
            'email' => 'ayse.yilmaz@ataturklisesi.edu.tr',
            'password' => 'teacher123', 
            'role' => 'Öğretmen'
        ]
    ];
    
    $results = [];
    
    foreach ($testUsers as $key => $userData) {
        try {
            // Login isteği simüle et
            $user = \App\Models\User::where('email', $userData['email'])->first();
            
            if ($user && \Hash::check($userData['password'], $user->password)) {
                // Token oluştur
                $token = $user->createToken('test-token')->plainTextToken;
                
                $results[$key] = [
                    'status' => 'SUCCESS',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->display_name,
                        'school' => $user->school ? $user->school->name : null,
                        'permissions' => $user->role->permissions
                    ],
                    'token' => $token,
                    'test_result' => '✅ Login başarılı'
                ];
            } else {
                $results[$key] = [
                    'status' => 'FAIL',
                    'test_result' => '❌ Login başarısız - Kullanıcı bulunamadı veya şifre hatalı'
                ];
            }
            
        } catch (Exception $e) {
            $results[$key] = [
                'status' => 'ERROR',
                'test_result' => '❌ Hata: ' . $e->getMessage()
            ];
        }
    }
    
    echo json_encode([
        'test_name' => 'LOGIN API TEST',
        'timestamp' => date('Y-m-d H:i:s'),
        'results' => $results,
        'summary' => [
            'total_tests' => count($testUsers),
            'passed' => count(array_filter($results, fn($r) => $r['status'] === 'SUCCESS')),
            'failed' => count(array_filter($results, fn($r) => $r['status'] !== 'SUCCESS'))
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'FATAL_ERROR',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>