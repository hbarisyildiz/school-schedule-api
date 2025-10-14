<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Kimlik doğrulama rotaları
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

// Genel erişim (auth gerektirmeyen)
Route::get('cities', [\App\Http\Controllers\Api\SchoolController::class, 'getCities']);
Route::get('cities/{cityId}/districts', [\App\Http\Controllers\Api\SchoolController::class, 'getDistricts']);
Route::get('subscription-plans', [\App\Http\Controllers\Api\SchoolController::class, 'getSubscriptionPlans']);

// Okul kayıt sistemi (auth gerektirmeyen)
Route::post('register-school', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'register']);
Route::post('verify-school-email', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'verifyEmail']);
Route::get('verify-school-email/{token}', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'verifyEmail']);

// Korumalı rotalar
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth user bilgileri
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Dashboard & Notifications
    Route::get('dashboard/stats', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    Route::get('dashboard/activities', [\App\Http\Controllers\Api\DashboardController::class, 'recentActivities']);
    Route::get('notifications', [\App\Http\Controllers\Api\DashboardController::class, 'notifications']);
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\DashboardController::class, 'markNotificationAsRead']);
    Route::post('notifications/read-all', [\App\Http\Controllers\Api\DashboardController::class, 'markAllNotificationsAsRead']);
    
    // Süper Admin rotaları
    Route::middleware(['role:super_admin'])->group(function () {
        Route::apiResource('schools', \App\Http\Controllers\Api\SchoolController::class);
        Route::post('schools/{id}/subscription', [\App\Http\Controllers\Api\SchoolController::class, 'updateSubscription']);
        Route::post('schools/{id}/toggle-status', [\App\Http\Controllers\Api\SchoolController::class, 'toggleStatus']);
        
        // Okul kayıt talepleri yönetimi
        Route::get('registration-requests', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'index']);
        Route::get('registration-requests/{id}', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'show']);
        Route::delete('registration-requests/{id}', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'destroy']);
        Route::post('registration-requests/{id}/approve', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'approve']);
        Route::post('registration-requests/{id}/reject', [\App\Http\Controllers\Api\SchoolRegistrationController::class, 'reject']);
    });
    

    
    // Okul bazlı rotalar (kendi okulu)
    Route::middleware(['school.access'])->group(function () {
        
        // Okul bilgileri
        Route::get('my-school', function () {
            $user = auth()->user();
            return response()->json([
                'school' => $user->school->load('subscriptionPlan'),
                'statistics' => [
                    'teachers' => $user->school->current_teachers,
                    'students' => $user->school->current_students,
                    'classes' => $user->school->current_classes,
                ]
            ]);
        });
        
        // Okul ayarları
        Route::get('school/settings', [\App\Http\Controllers\Api\SchoolController::class, 'getSettings']);
        Route::put('school/settings', [\App\Http\Controllers\Api\SchoolController::class, 'updateSettings']);
        
        // Tenefüs süreleri
        Route::get('school/break-durations', [\App\Http\Controllers\Api\SchoolController::class, 'getBreakDurations']);
        Route::put('school/break-durations', [\App\Http\Controllers\Api\SchoolController::class, 'updateBreakDurations']);
        
        // Sınıf günlük ders programları
        Route::get('school/class-daily-schedules', [\App\Http\Controllers\Api\SchoolController::class, 'getClassDailySchedules']);
        Route::put('school/class-daily-schedules/{classId}', [\App\Http\Controllers\Api\SchoolController::class, 'updateClassDailySchedule']);
        
        // Öğretmen günlük ders programları
        Route::get('school/teacher-daily-schedules', [\App\Http\Controllers\Api\SchoolController::class, 'getTeacherDailySchedules']);
        Route::put('school/teacher-daily-schedules/{teacherId}', [\App\Http\Controllers\Api\SchoolController::class, 'updateTeacherDailySchedule']);
        
        // Kullanıcı yönetimi
        Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
        Route::post('users/{id}/toggle-status', [\App\Http\Controllers\Api\UserController::class, 'toggleStatus']);
        Route::post('users/{id}/password', [\App\Http\Controllers\Api\UserController::class, 'updatePassword']);
        Route::post('users/import-teachers', [\App\Http\Controllers\Api\UserController::class, 'importTeachers']);
        Route::get('roles', [\App\Http\Controllers\Api\UserController::class, 'getRoles']);
        
        // Sınıf yönetimi
        Route::apiResource('classes', \App\Http\Controllers\Api\ClassController::class);
        Route::post('classes/{id}/toggle-status', [\App\Http\Controllers\Api\ClassController::class, 'toggleStatus']);
        Route::get('teachers', [\App\Http\Controllers\Api\ClassController::class, 'getTeachers']);
        Route::post('profile', [\App\Http\Controllers\Api\UserController::class, 'updateProfile']);
        
        // Ders programı yönetimi
        Route::apiResource('schedules', \App\Http\Controllers\Api\ScheduleController::class);
        Route::get('schedules/weekly/view', [\App\Http\Controllers\Api\ScheduleController::class, 'weeklyView']);
        Route::get('schedules/teacher/{teacherId?}', [\App\Http\Controllers\Api\ScheduleController::class, 'teacherSchedule']);
        Route::get('schedules/class/{classId}', [\App\Http\Controllers\Api\ScheduleController::class, 'classSchedule']);
        Route::post('schedules/generate', [\App\Http\Controllers\Api\ScheduleController::class, 'generateSchedule']);
        
        // Ders yönetimi
        Route::apiResource('subjects', \App\Http\Controllers\Api\SubjectController::class);
        Route::post('subjects/{id}/toggle-status', [\App\Http\Controllers\Api\SubjectController::class, 'toggleStatus']);
        Route::get('subject-templates', [\App\Http\Controllers\Api\SubjectController::class, 'getTemplates']);
        // Route::get('classes', ...) - KALDIRILDI! Yukarıda apiResource var (satır 84)
        Route::get('teachers', function () {
            return response()->json(\App\Models\User::where('school_id', auth()->user()->school_id)
                ->whereHas('role', function($query) {
                    $query->where('name', 'teacher');
                })->get(['id', 'name', 'email']));
        });
        
    });
    
    // Test rotası
    Route::get('test', function () {
        return response()->json([
            'message' => 'API çalışıyor!',
            'user' => auth()->user()->name,
            'role' => auth()->user()->role->display_name,
            'school' => auth()->user()->school->name ?? 'Okul yok',
            'permissions' => auth()->user()->role->permissions
        ]);
    });
});