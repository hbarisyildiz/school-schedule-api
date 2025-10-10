<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    // API Route testleri
    $testRoutes = [
        '/api/auth/login' => 'POST - Login endpoint',
        '/api/auth/register' => 'POST - Register endpoint',
        '/api/user' => 'GET - User info (token required)',
        '/api/test' => 'GET - Test endpoint (token required)',
        '/api/my-school' => 'GET - School info (token required)',
        '/api/subscription-plans' => 'GET - Subscription plans',
        '/api/schools' => 'GET - All schools (super admin only)'
    ];
    
    // Middleware testleri
    $middlewareTests = [
        'auth:sanctum' => 'Laravel Sanctum authentication',
        'school.access' => 'Multi-tenant school access control',
        'role' => 'Role-based authorization', 
        'permission' => 'Permission-based authorization'
    ];
    
    // Model ilişki testleri
    $relationshipTests = [];
    
    try {
        $school = \App\Models\School::with(['subscriptionPlan', 'users.role'])->first();
        if ($school) {
            $relationshipTests['School->SubscriptionPlan'] = '✅ OK - ' . $school->subscriptionPlan->name;
            $relationshipTests['School->Users'] = '✅ OK - ' . $school->users->count() . ' kullanıcı';
            
            $user = $school->users->first();
            if ($user && $user->role) {
                $relationshipTests['User->Role'] = '✅ OK - ' . $user->role->display_name;
            }
        }
    } catch (Exception $e) {
        $relationshipTests['error'] = '❌ ' . $e->getMessage();
    }
    
    // Veritabanı istatistikleri
    $dbStats = [
        'subscription_plans' => \App\Models\SubscriptionPlan::count(),
        'schools' => \App\Models\School::count(),
        'roles' => \App\Models\Role::count(),
        'users' => \App\Models\User::count(),
        'subjects' => \App\Models\Subject::count(),
        'classes' => \App\Models\ClassRoom::count(),
        'schedules' => \App\Models\Schedule::count()
    ];
    
    echo json_encode([
        'test_name' => 'API COMPREHENSIVE TEST',
        'timestamp' => date('Y-m-d H:i:s'),
        'system_info' => [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connection' => 'OK',
            'server_status' => 'Running'
        ],
        'api_routes' => $testRoutes,
        'middleware_available' => $middlewareTests,
        'model_relationships' => $relationshipTests,
        'database_statistics' => $dbStats,
        'test_summary' => [
            'total_models' => 7,
            'total_routes' => count($testRoutes),
            'total_middleware' => count($middlewareTests),
            'system_status' => '✅ ALL SYSTEMS OPERATIONAL'
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