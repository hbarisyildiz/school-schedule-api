<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard istatistiklerini getir
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->role->name === 'super_admin';
        
        // Super Admin tüm sistem istatistiklerini görür
        if ($isSuperAdmin) {
            $stats = [
                'total_schools' => School::count(),
                'active_schools' => School::where('is_active', true)->count(),
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_teachers' => User::whereHas('role', function($q) {
                    $q->where('name', 'teacher');
                })->count(),
                'total_students' => User::whereHas('role', function($q) {
                    $q->where('name', 'student');
                })->count(),
                'total_classes' => ClassRoom::count(),
                'active_classes' => ClassRoom::where('is_active', true)->count(),
                'total_subjects' => Subject::count(),
                'total_schedules' => Schedule::count(),
                
                // Abonelik istatistikleri
                'active_subscriptions' => School::where('subscription_status', 'active')->count(),
                'expired_subscriptions' => School::where('subscription_status', 'expired')->count(),
                'trial_ending_soon' => School::where('subscription_ends_at', '<=', now()->addDays(7))
                    ->where('subscription_status', 'active')
                    ->count(),
                
                // Bu ay
                'schools_this_month' => School::whereMonth('created_at', now()->month)->count(),
                'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                
                // Bugün
                'active_today' => User::whereDate('last_login_at', today())->count(),
            ];
        } else {
            // Okul yöneticisi sadece kendi okulunu görür
            $schoolId = $user->school_id;
            
            $stats = [
                'school_name' => $user->school->name,
                'school_code' => $user->school->code,
                'total_teachers' => User::where('school_id', $schoolId)
                    ->whereHas('role', function($q) {
                        $q->where('name', 'teacher');
                    })->count(),
                'total_students' => User::where('school_id', $schoolId)
                    ->whereHas('role', function($q) {
                        $q->where('name', 'student');
                    })->count(),
                'total_classes' => ClassRoom::where('school_id', $schoolId)->count(),
                'active_classes' => ClassRoom::where('school_id', $schoolId)
                    ->where('is_active', true)->count(),
                'total_subjects' => Subject::where('school_id', $schoolId)->count(),
                'total_schedules' => Schedule::where('school_id', $schoolId)->count(),
                'active_schedules' => Schedule::where('school_id', $schoolId)
                    ->where('is_active', true)->count(),
                
                // Abonelik bilgileri
                'subscription_plan' => $user->school->subscriptionPlan->name,
                'subscription_status' => $user->school->subscription_status,
                'subscription_ends_at' => $user->school->subscription_ends_at->format('d.m.Y'),
                'days_left' => $user->school->subscription_ends_at->diffInDays(now()),
                
                // Bu hafta
                'schedules_this_week' => Schedule::where('school_id', $schoolId)
                    ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                
                // Bildirimler
                'unread_notifications' => Notification::where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
            ];
        }
        
        return response()->json([
            'stats' => $stats,
            'user_type' => $isSuperAdmin ? 'super_admin' : 'school_admin'
        ]);
    }
    
    /**
     * Son aktiviteleri getir
     */
    public function recentActivities(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->role->name === 'super_admin';
        
        $query = \App\Models\ActivityLog::with(['user', 'school'])
            ->orderBy('created_at', 'desc')
            ->limit(20);
            
        if (!$isSuperAdmin) {
            $query->where('school_id', $user->school_id);
        }
        
        $activities = $query->get();
        
        return response()->json($activities);
    }
    
    /**
     * Bildirimler
     */
    public function notifications(Request $request)
    {
        $user = $request->user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('read_at', null)->count()
        ]);
    }
    
    /**
     * Bildirimi okundu olarak işaretle
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->findOrFail($id);
            
        $notification->markAsRead();
        
        return response()->json(['message' => 'Bildirim okundu olarak işaretlendi']);
    }
    
    /**
     * Tüm bildirimleri okundu olarak işaretle
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['message' => 'Tüm bildirimler okundu olarak işaretlendi']);
    }
}
