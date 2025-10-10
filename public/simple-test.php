<?php
// Basit test dosyası
echo "PHP Test - " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";

try {
    require_once '../vendor/autoload.php';
    echo "Laravel autoload: OK<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "Laravel app: OK<br>";
    echo "Laravel Version: " . app()->version() . "<br>";
    
    // Basit model testi
    $userCount = \App\Models\User::count();
    echo "User count: " . $userCount . "<br>";
    
    $schoolCount = \App\Models\School::count();
    echo "School count: " . $schoolCount . "<br>";
    
    echo "<h3>✅ Sistem çalışıyor!</h3>";
    
} catch (Exception $e) {
    echo "<h3>❌ Hata: " . $e->getMessage() . "</h3>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>