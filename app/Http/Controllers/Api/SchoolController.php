<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    /**
     * Tüm okulları listele (Sadece süper admin)
     */
    public function index(Request $request)
    {
        $schools = School::with(['subscriptionPlan', 'city', 'district'])
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                $query->where('subscription_status', $status);
            })
            ->paginate(15);

        return response()->json($schools);
    }

    /**
     * Yeni okul oluştur (Süper admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id'
        ]);

        // Database transaction ile okul ve yönetici kullanıcısını oluştur
        DB::beginTransaction();
        
        try {
            // Okul oluştur
            $school = School::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'code' => 'SCH' . rand(1000, 9999),
                'email' => $request->email,
                'phone' => $request->phone,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'website' => $request->website,
                'subscription_plan_id' => $request->subscription_plan_id,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addMonth(),
                'subscription_status' => 'active',
                'is_active' => true
            ]);

            // School Admin rolünü bul
            $schoolAdminRole = \App\Models\Role::where('name', 'school_admin')->first();
            
            if (!$schoolAdminRole) {
                throw new \Exception('School admin rolü bulunamadı');
            }

            // Okul yöneticisi kullanıcısı oluştur
            $schoolAdmin = \App\Models\User::create([
                'name' => $request->name . ' Yöneticisi',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'school_id' => $school->id,
                'role_id' => $schoolAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Okul ve yönetici hesabı başarıyla oluşturuldu',
                'school' => $school->load(['subscriptionPlan', 'city', 'district']),
                'admin' => [
                    'name' => $schoolAdmin->name,
                    'email' => $schoolAdmin->email,
                    'role' => 'school_admin'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Okul oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Okul detaylarını göster
     */
    public function show(string $id)
    {
        $school = School::with(['subscriptionPlan', 'users.role'])
            ->findOrFail($id);

        return response()->json([
            'school' => $school,
            'statistics' => [
                'teachers' => $school->current_teachers,
                'students' => $school->current_students,
                'classes' => $school->current_classes,
                'subscription_days_left' => $school->subscription_ends_at->diffInDays(now())
            ]
        ]);
    }

    /**
     * Okul bilgilerini güncelle
     */
    public function update(Request $request, string $id)
    {
        $school = School::findOrFail($id);
        
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:schools,email,' . $school->id,
            'phone' => 'string',
            'city_id' => 'exists:cities,id',
            'district_id' => 'exists:districts,id',
        ]);

        $school->update($request->only([
            'name', 'email', 'phone', 'city_id', 'district_id', 'website'
        ]));

        return response()->json([
            'message' => 'Okul bilgileri güncellendi',
            'school' => $school->fresh()->load(['subscriptionPlan', 'city', 'district'])
        ]);
    }

    /**
     * Okulun aboneliğini güncelle (Süper admin)
     */
    public function updateSubscription(Request $request, string $id)
    {
        $school = School::findOrFail($id);
        
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'subscription_ends_at' => 'required|date|after:today',
            'subscription_status' => 'required|in:active,expired,suspended,cancelled'
        ]);

        $school->update([
            'subscription_plan_id' => $request->subscription_plan_id,
            'subscription_ends_at' => $request->subscription_ends_at,
            'subscription_status' => $request->subscription_status
        ]);

        return response()->json([
            'message' => 'Abonelik güncellendi',
            'school' => $school->fresh()->load('subscriptionPlan')
        ]);
    }

    /**
     * Okulu pasifleştir/aktifleştir
     */
    public function toggleStatus(string $id)
    {
        $school = School::findOrFail($id);
        $school->update(['is_active' => !$school->is_active]);

        return response()->json([
            'message' => $school->is_active ? 'Okul aktifleştirildi' : 'Okul pasifleştirildi',
            'school' => $school
        ]);
    }

    /**
     * Abonelik planlarını listele
     */
    public function getSubscriptionPlans()
    {
        $plans = SubscriptionPlan::active()->get();
        
        return response()->json($plans);
    }

    /**
     * Şehirleri listele
     */
    public function getCities()
    {
        $cities = \App\Models\City::orderBy('name')->get();
        
        return response()->json($cities);
    }

    /**
     * Belirli şehre ait ilçeleri listele
     */
    public function getDistricts($cityId)
    {
        $districts = \App\Models\District::where('city_id', $cityId)
            ->orderBy('name')
            ->get();
            
        return response()->json($districts);
    }

    /**
     * Okul ayarlarını getir (Okul müdürü için)
     */
    public function getSettings(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        return response()->json([
            'class_days' => $school->getDefaultClassDays(),
            'lesson_duration' => $school->getDefaultLessonDuration(),
            'break_durations' => $school->getDefaultBreakDurations(),
            'school_hours' => $school->getDefaultSchoolHours(),
            'weekly_lesson_count' => $school->getDefaultWeeklyLessonCount(),
            'schedule_settings' => $school->getDefaultScheduleSettings(),
            'class_days_turkish' => $school->getClassDaysInTurkish(),
            'daily_lesson_counts' => $school->getDailyLessonCounts(),
            'class_daily_lesson_counts' => $school->getClassDailyLessonCounts(),
            'teacher_daily_lesson_counts' => $school->teacher_daily_lesson_counts ?? []
        ]);
    }

    /**
     * Okul ayarlarını güncelle (Okul müdürü için)
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        $request->validate([
            'class_days' => 'array',
            'class_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'lesson_duration' => 'integer|min:20|max:120',
            'break_durations' => 'array',
            'break_durations.small_break' => 'integer|min:5|max:30',
            'break_durations.lunch_break' => 'integer|min:10|max:60',
            'school_hours' => 'array',
            'school_hours.start_time' => 'date_format:H:i',
            'school_hours.end_time' => 'date_format:H:i|after:school_hours.start_time',
            'weekly_lesson_count' => 'integer|min:20|max:50',
            'schedule_settings' => 'array',
            'schedule_settings.allow_teacher_conflicts' => 'boolean',
            'schedule_settings.allow_classroom_conflicts' => 'boolean',
            'schedule_settings.max_lessons_per_day' => 'integer|min:1|max:12',
            'schedule_settings.min_lessons_per_day' => 'integer|min:1|max:12',
            'daily_lesson_counts' => 'array',
            'daily_lesson_counts.*' => 'integer|min:1|max:12',
            'class_daily_lesson_counts' => 'array',
            'teacher_daily_lesson_counts' => 'array'
        ]);

        // Sadece gönderilen alanları güncelle
        $updateData = $request->only([
            'class_days',
            'lesson_duration',
            'break_durations',
            'school_hours',
            'weekly_lesson_count',
            'schedule_settings',
            'daily_lesson_counts'
        ]);
        
        // class_daily_lesson_counts için merge yap (mevcut veriyi koru)
        if ($request->has('class_daily_lesson_counts')) {
            $existingClassCounts = $school->class_daily_lesson_counts ?? [];
            $newClassCounts = $request->input('class_daily_lesson_counts', []);
            $updateData['class_daily_lesson_counts'] = array_merge($existingClassCounts, $newClassCounts);
        }
        
        // teacher_daily_lesson_counts için merge yap (mevcut veriyi koru)
        if ($request->has('teacher_daily_lesson_counts')) {
            $existingTeacherCounts = $school->teacher_daily_lesson_counts ?? [];
            $newTeacherCounts = $request->input('teacher_daily_lesson_counts', []);
            $updateData['teacher_daily_lesson_counts'] = array_merge($existingTeacherCounts, $newTeacherCounts);
        }
        
        $school->update($updateData);

        return response()->json([
            'message' => 'Okul ayarları başarıyla güncellendi',
            'settings' => [
                'class_days' => $school->getDefaultClassDays(),
                'lesson_duration' => $school->getDefaultLessonDuration(),
                'break_durations' => $school->getDefaultBreakDurations(),
                'school_hours' => $school->getDefaultSchoolHours(),
                'weekly_lesson_count' => $school->getDefaultWeeklyLessonCount(),
                'schedule_settings' => $school->getDefaultScheduleSettings(),
                'class_days_turkish' => $school->getClassDaysInTurkish(),
                'daily_lesson_counts' => $school->getDailyLessonCounts(),
                'class_daily_lesson_counts' => $school->getClassDailyLessonCounts(),
                'teacher_daily_lesson_counts' => $school->teacher_daily_lesson_counts ?? []
            ]
        ]);
    }
}
