<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    $results = [];
    
    // Test kullanıcıları
    $testEmails = [
        'admin@schoolschedule.com',
        'mudur@ataturklisesi.edu.tr',
        'ayse.yilmaz@ataturklisesi.edu.tr'
    ];
    
    foreach ($testEmails as $email) {
        try {
            $user = \App\Models\User::with(['role', 'school'])->where('email', $email)->first();
            
            if ($user) {
                $results[] = [
                    'email' => $email,
                    'status' => 'SUCCESS',
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->display_name,
                        'role_level' => $user->role->level,
                        'school' => $user->school ? $user->school->name : 'Okul atanmamış',
                        'permissions' => $user->role->permissions,
                        'is_active' => $user->is_active,
                        'last_login' => $user->last_login_at,
                        'created_at' => $user->created_at->format('d.m.Y H:i')
                    ],
                    'test_result' => '✅ Kullanıcı bilgileri alındı'
                ];
            } else {
                $results[] = [
                    'email' => $email,
                    'status' => 'FAIL',
                    'test_result' => '❌ Kullanıcı bulunamadı'
                ];
            }
            
        } catch (Exception $e) {
            $results[] = [
                'email' => $email,
                'status' => 'ERROR',
                'test_result' => '❌ Hata: ' . $e->getMessage()
            ];
        }
    }
    
    echo json_encode([
        'test_name' => 'USER INFO API TEST',
        'timestamp' => date('Y-m-d H:i:s'),
        'results' => $results,
        'summary' => [
            'total_tests' => count($testEmails),
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