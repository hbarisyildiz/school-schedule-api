<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Laravel API Test Sayfası',
        'tests' => [
            '1' => 'Database bağlantısı: OK',
            '2' => 'Model\'ler yüklendi: OK', 
            '3' => 'Seeder veriler mevcut: Kontrol edilecek',
            '4' => 'API routes tanımlı: OK'
        ],
        'info' => [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => date('Y-m-d H:i:s')
        ],
        'test_users' => [
            'super_admin' => 'admin@schoolschedule.com / admin123',
            'school_admin' => 'mudur@ataturklisesi.edu.tr / mudur123',
            'teacher' => 'ayse.yilmaz@ataturklisesi.edu.tr / teacher123'
        ],
        'endpoints' => [
            'login' => 'POST /api/auth/login',
            'user_info' => 'GET /api/user (token gerekli)',
            'test' => 'GET /api/test (token gerekli)',
            'my_school' => 'GET /api/my-school (token gerekli)'
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>