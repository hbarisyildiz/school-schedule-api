<?php
// API Test Script
header('Content-Type: application/json');

// Test 1: Database connection
try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Laravel API çalışıyor',
        'php_version' => PHP_VERSION,
        'laravel_version' => $app->version(),
        'database' => 'Connected',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'php_version' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>