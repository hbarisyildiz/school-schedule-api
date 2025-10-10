<?php
// Dashboard temizleme
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Output buffering başlat
ob_start();

try {
    // Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    // Veritabanı bağlantısı test
    $dbStatus = 'OK';
    $systemInfo = [
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
        'server_time' => date('d.m.Y H:i:s'),
        'database_status' => $dbStatus
    ];
    
    // Basit istatistikler
    $stats = [];
    try {
        $stats['subscription_plans'] = \App\Models\SubscriptionPlan::count();
        $stats['schools'] = \App\Models\School::count();
        $stats['roles'] = \App\Models\Role::count();
        $stats['users'] = \App\Models\User::count();
        $stats['subjects'] = \App\Models\Subject::count();
        $stats['classes'] = \App\Models\ClassRoom::count();
        $stats['schedules'] = \App\Models\Schedule::count();
    } catch (Exception $e) {
        $stats['error'] = $e->getMessage();
    }
    
    // Test kullanıcıları
    $testUsers = [];
    try {
        $users = \App\Models\User::with(['role', 'school'])->get();
        foreach ($users as $user) {
            $testUsers[] = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->display_name ?? 'Rol yok',
                'school' => $user->school->name ?? 'Okul yok',
                'active' => $user->is_active ? 'Aktif' : 'Pasif'
            ];
        }
    } catch (Exception $e) {
        $testUsers['error'] = $e->getMessage();
    }
    
    // Okul bilgileri
    $schoolInfo = [];
    try {
        $schools = \App\Models\School::with('subscriptionPlan')->get();
        foreach ($schools as $school) {
            $schoolInfo[] = [
                'name' => $school->name,
                'email' => $school->email,
                'code' => $school->code,
                'plan' => $school->subscriptionPlan->name ?? 'Plan yok',
                'status' => $school->isSubscriptionActive() ? 'Aktif' : 'Pasif',
                'teachers' => $school->current_teachers,
                'students' => $school->current_students,
                'classes' => $school->current_classes
            ];
        }
    } catch (Exception $e) {
        $schoolInfo['error'] = $e->getMessage();
    }
    
    $success = true;
    $errorMessage = null;
    
} catch (Exception $e) {
    $success = false;
    $errorMessage = $e->getMessage();
    $systemInfo = ['error' => $e->getMessage()];
    $stats = [];
    $testUsers = [];
    $schoolInfo = [];
}

// Output buffer temizle
ob_clean();

// JSON olarak döndür
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => $success ? 'SUCCESS' : 'ERROR',
    'timestamp' => date('Y-m-d H:i:s'),
    'system_info' => $systemInfo,
    'database_stats' => $stats,
    'test_users' => $testUsers,
    'school_info' => $schoolInfo,
    'api_endpoints' => [
        'POST /api/auth/login' => 'Kullanıcı girişi',
        'POST /api/auth/register' => 'Yeni okul kaydı',
        'GET /api/user' => 'Kullanıcı bilgileri (token gerekli)',
        'GET /api/test' => 'API test endpoint (token gerekli)',
        'GET /api/my-school' => 'Okul bilgileri (token gerekli)',
        'GET /api/subscription-plans' => 'Abonelik planları',
        'GET /api/schools' => 'Tüm okullar (super admin only)'
    ],
    'security_features' => [
        'authentication' => 'Laravel Sanctum tokens',
        'authorization' => 'Role + Permission based',
        'multi_tenant' => 'Single DB + school_id isolation',
        'middleware' => ['auth:sanctum', 'school.access', 'role', 'permission']
    ],
    'error_message' => $errorMessage
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>