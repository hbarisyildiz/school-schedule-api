<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    $schools = \App\Models\School::with(['subscriptionPlan', 'users.role'])->get();
    
    $schoolTests = [];
    
    foreach ($schools as $school) {
        $schoolData = [
            'id' => $school->id,
            'name' => $school->name,
            'email' => $school->email,
            'code' => $school->code,
            'subscription' => [
                'plan' => $school->subscriptionPlan->name,
                'status' => $school->subscription_status,
                'starts_at' => $school->subscription_starts_at->format('d.m.Y'),
                'ends_at' => $school->subscription_ends_at->format('d.m.Y'),
                'days_left' => $school->subscription_ends_at->diffInDays(now()),
                'is_active' => $school->isSubscriptionActive() ? '✅ Aktif' : '❌ Pasif'
            ],
            'statistics' => [
                'current_teachers' => $school->current_teachers,
                'current_students' => $school->current_students,
                'current_classes' => $school->current_classes,
                'total_users' => $school->users->count()
            ],
            'limits' => [
                'max_teachers' => $school->subscriptionPlan->max_teachers ?? 'Sınırsız',
                'max_students' => $school->subscriptionPlan->max_students ?? 'Sınırsız',
                'max_classes' => $school->subscriptionPlan->max_classes ?? 'Sınırsız'
            ],
            'users_by_role' => []
        ];
        
        // Rol bazında kullanıcı sayıları
        foreach ($school->users->groupBy('role.name') as $roleName => $users) {
            $schoolData['users_by_role'][$roleName] = [
                'count' => $users->count(),
                'role_display' => $users->first()->role->display_name ?? $roleName
            ];
        }
        
        // Limit kontrolleri
        $schoolData['can_add'] = [
            'teacher' => $school->canAddTeacher() ? '✅ Evet' : '❌ Limit doldu',
            'student' => $school->canAddStudent() ? '✅ Evet' : '❌ Limit doldu', 
            'class' => $school->canAddClass() ? '✅ Evet' : '❌ Limit doldu'
        ];
        
        $schoolTests[] = $schoolData;
    }
    
    echo json_encode([
        'test_name' => 'SCHOOL INFO TEST',
        'timestamp' => date('Y-m-d H:i:s'),
        'total_schools' => count($schools),
        'schools' => $schoolTests,
        'summary' => [
            'active_subscriptions' => $schools->where('subscription_status', 'active')->count(),
            'total_users' => \App\Models\User::count(),
            'total_plans' => \App\Models\SubscriptionPlan::count(),
            'system_status' => '✅ Multi-tenant system operational'
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