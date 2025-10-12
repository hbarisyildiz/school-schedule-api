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
            // Tek sorguda tüm user role sayılarını al
            $userCounts = User::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN DATE(last_login_at) = CURDATE() THEN 1 ELSE 0 END) as active_today,
                SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) as this_month
            ')->first();
            
            // Role bazlı sayılar
            $teacherCount = \DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'teacher')
                ->count();
                
            $studentCount = \DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'student')
                ->count();
            
            // Okul istatistikleri tek sorguda
            $schoolCounts = School::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN subscription_status = "active" THEN 1 ELSE 0 END) as active_subs,
                SUM(CASE WHEN subscription_status = "expired" THEN 1 ELSE 0 END) as expired_subs,
                SUM(CASE WHEN subscription_ends_at <= DATE_ADD(NOW(), INTERVAL 7 DAY) AND subscription_status = "active" THEN 1 ELSE 0 END) as ending_soon,
                SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) as this_month
            ')->first();
            
            $stats = [
                'total_schools' => $schoolCounts->total,
                'active_schools' => $schoolCounts->active,
                'total_users' => $userCounts->total,
                'active_users' => $userCounts->active,
                'total_teachers' => $teacherCount,
                'total_students' => $studentCount,
                'total_classes' => ClassRoom::count(),
                'active_classes' => ClassRoom::where('is_active', true)->count(),
                'total_subjects' => Subject::count(),
                'total_schedules' => Schedule::count(),
                'active_subscriptions' => $schoolCounts->active_subs,
                'expired_subscriptions' => $schoolCounts->expired_subs,
                'trial_ending_soon' => $schoolCounts->ending_soon,
                'schools_this_month' => $schoolCounts->this_month,
                'users_this_month' => $userCounts->this_month,
                'active_today' => $userCounts->active_today,
            ];
        } else {
            // Okul yöneticisi sadece kendi okulunu görür
            $schoolId = $user->school_id;
            
            // Tek sorguda okul bazlı sayılar
            $teacherCount = \DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('users.school_id', $schoolId)
                ->where('roles.name', 'teacher')
                ->count();
                
            $studentCount = \DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('users.school_id', $schoolId)
                ->where('roles.name', 'student')
                ->count();
            
            $classCounts = ClassRoom::where('school_id', $schoolId)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
                ')->first();
            
            $scheduleCounts = Schedule::where('school_id', $schoolId)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN start_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as this_week
                ', [now()->startOfWeek(), now()->endOfWeek()])
                ->first();
            
            $stats = [
                'school_name' => $user->school->name,
                'school_code' => $user->school->code,
                'total_teachers' => $teacherCount,
                'total_students' => $studentCount,
                'total_classes' => $classCounts->total,
                'active_classes' => $classCounts->active,
                'total_subjects' => Subject::where('school_id', $schoolId)->count(),
                'total_schedules' => $scheduleCounts->total,
                'active_schedules' => $scheduleCounts->active,
                'subscription_plan' => $user->school->subscriptionPlan->name,
                'subscription_status' => $user->school->subscription_status,
                'subscription_ends_at' => $user->school->subscription_ends_at->format('d.m.Y'),
                'days_left' => $user->school->subscription_ends_at->diffInDays(now()),
                'schedules_this_week' => $scheduleCounts->this_week,
                'unread_notifications' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
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
