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
            'school_type' => 'required|in:ilkokul,ortaokul,lise',
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
                'school_type' => $request->school_type,
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

            // Okul türüne göre otomatik sınıflar oluştur
            $this->createDefaultClasses($school, $request->school_type);

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
            'school_type' => $school->school_type,
            'grade_levels' => $school->getGradeLevels(),
            'class_days' => $school->getDefaultClassDays(),
            'lesson_duration' => $school->getDefaultLessonDuration(),
            'daily_lesson_count' => $school->daily_lesson_count ?? 8,
            'break_durations' => $school->getDefaultBreakDurations(),
            'school_hours' => $school->getDefaultSchoolHours(),
            'weekly_lesson_count' => $school->getDefaultWeeklyLessonCount(),
            'schedule_settings' => $school->getDefaultScheduleSettings(),
            'class_days_turkish' => $school->getClassDaysInTurkish(),
            'daily_lesson_counts' => $school->getDailyLessonCounts()
            // class_daily_lesson_counts ve teacher_daily_lesson_counts artık ayrı endpoint'lerden çekiliyor
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
            'school_type' => 'nullable|in:ilkokul,ortaokul,lise',
            'class_days' => 'array',
            'class_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'lesson_duration' => 'integer|min:20|max:120',
            'daily_lesson_count' => 'integer|min:1|max:12',
            'break_durations' => 'array',
            'break_durations.break_1' => 'integer|min:5|max:30',
            'break_durations.break_2' => 'integer|min:5|max:30',
            'break_durations.break_3' => 'integer|min:5|max:30',
            'break_durations.break_4' => 'integer|min:5|max:30',
            'break_durations.break_5' => 'integer|min:5|max:30',
            'break_durations.break_6' => 'integer|min:5|max:30',
            'break_durations.break_7' => 'integer|min:5|max:30',
            'break_durations.break_8' => 'integer|min:5|max:30',
            'break_durations.break_9' => 'integer|min:5|max:30',
            'school_hours' => 'array',
            'school_hours.start' => 'date_format:H:i',
            'weekly_lesson_count' => 'integer|min:20|max:50',
            'schedule_settings' => 'array',
            'schedule_settings.allow_teacher_conflicts' => 'boolean',
            'schedule_settings.allow_classroom_conflicts' => 'boolean',
            'schedule_settings.max_lessons_per_day' => 'integer|min:1|max:12',
            'schedule_settings.min_lessons_per_day' => 'integer|min:1|max:12',
            'daily_lesson_counts' => 'array',
            'daily_lesson_counts.*' => 'integer|min:1|max:12'
        ]);

        // Sadece gönderilen alanları güncelle
        $updateData = $request->only([
            'school_type',
            'class_days',
            'lesson_duration',
            'daily_lesson_count',
            'break_durations',
            'school_hours',
            'weekly_lesson_count',
            'schedule_settings',
            'daily_lesson_counts'
        ]);
        
        $school->update($updateData);

        return response()->json([
            'message' => 'Okul ayarları başarıyla güncellendi',
            'settings' => [
                'school_type' => $school->school_type,
                'grade_levels' => $school->getGradeLevels(),
                'class_days' => $school->getDefaultClassDays(),
                'lesson_duration' => $school->getDefaultLessonDuration(),
                'daily_lesson_count' => $school->daily_lesson_count ?? 8,
                'break_durations' => $school->getDefaultBreakDurations(),
                'school_hours' => $school->getDefaultSchoolHours(),
                'weekly_lesson_count' => $school->getDefaultWeeklyLessonCount(),
                'schedule_settings' => $school->getDefaultScheduleSettings(),
                'class_days_turkish' => $school->getClassDaysInTurkish(),
                'daily_lesson_counts' => $school->getDailyLessonCounts()
                // class_daily_lesson_counts ve teacher_daily_lesson_counts artık ayrı endpoint'lerden çekiliyor
            ]
        ]);
    }

    /**
     * Okul tenefüs sürelerini getir
     */
    public function getBreakDurations(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        $breakDurations = \App\Models\SchoolBreakDuration::where('school_id', $school->id)
            ->orderBy('after_period')
            ->get();

        return response()->json($breakDurations);
    }

    /**
     * Okul tenefüs sürelerini güncelle
     */
    public function updateBreakDurations(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        $request->validate([
            'breaks' => 'required|array',
            'breaks.*.after_period' => 'required|integer|min:1|max:12',
            'breaks.*.duration' => 'required|integer|min:5|max:60',
            'breaks.*.is_lunch_break' => 'boolean'
        ]);

        // Mevcut tenefüsleri sil
        \App\Models\SchoolBreakDuration::where('school_id', $school->id)->delete();

        // Yeni tenefüsleri ekle
        foreach ($request->breaks as $break) {
            \App\Models\SchoolBreakDuration::create([
                'school_id' => $school->id,
                'after_period' => $break['after_period'],
                'duration' => $break['duration'],
                'is_lunch_break' => $break['is_lunch_break'] ?? false
            ]);
        }

        return response()->json([
            'message' => 'Tenefüs süreleri başarıyla güncellendi',
            'breaks' => \App\Models\SchoolBreakDuration::where('school_id', $school->id)
                ->orderBy('after_period')
                ->get()
        ]);
    }

    /**
     * Sınıf günlük ders programlarını getir
     */
    public function getClassDailySchedules(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        // Optimize edilmiş sorgu - whereHas yerine join kullan
        $schedules = \App\Models\ClassDailySchedule::select('class_daily_schedules.*')
            ->join('classes', 'class_daily_schedules.class_id', '=', 'classes.id')
            ->where('classes.school_id', $school->id)
            ->where('classes.is_active', true)
            ->get();

        return response()->json($schedules);
    }

    /**
     * Sınıf günlük ders programını güncelle
     */
    public function updateClassDailySchedule(Request $request, $classId)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.lesson_count' => 'required|integer|min:0|max:12'
        ]);

        $class = \App\Models\ClassRoom::where('id', $classId)
            ->where('school_id', $school->id)
            ->firstOrFail();

        // Mevcut programları sil
        \App\Models\ClassDailySchedule::where('class_id', $classId)->delete();

        // Yeni programları ekle
        foreach ($request->schedules as $schedule) {
            \App\Models\ClassDailySchedule::create([
                'class_id' => $classId,
                'day' => $schedule['day'],
                'lesson_count' => $schedule['lesson_count']
            ]);
        }

        return response()->json([
            'message' => 'Sınıf ders programı başarıyla güncellendi',
            'schedules' => \App\Models\ClassDailySchedule::where('class_id', $classId)->get()
        ]);
    }

    /**
     * Öğretmen günlük ders programlarını getir
     */
    public function getTeacherDailySchedules(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        // Optimize edilmiş sorgu - whereHas yerine join kullan
        $schedules = \App\Models\TeacherDailySchedule::select('teacher_daily_schedules.*')
            ->join('users', 'teacher_daily_schedules.teacher_id', '=', 'users.id')
            ->where('users.school_id', $school->id)
            ->where('users.is_active', true)
            ->get();

        return response()->json($schedules);
    }

    /**
     * Öğretmen günlük ders programını güncelle
     */
    public function updateTeacherDailySchedule(Request $request, $teacherId)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'Okul bulunamadı'], 404);
        }

        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.lesson_count' => 'required|integer|min:0|max:12'
        ]);

        $teacher = \App\Models\User::where('id', $teacherId)
            ->where('school_id', $school->id)
            ->firstOrFail();

        // Mevcut programları sil
        \App\Models\TeacherDailySchedule::where('teacher_id', $teacherId)->delete();

        // Yeni programları ekle
        foreach ($request->schedules as $schedule) {
            \App\Models\TeacherDailySchedule::create([
                'teacher_id' => $teacherId,
                'day' => $schedule['day'],
                'lesson_count' => $schedule['lesson_count']
            ]);
        }

        return response()->json([
            'message' => 'Öğretmen ders programı başarıyla güncellendi',
            'schedules' => \App\Models\TeacherDailySchedule::where('teacher_id', $teacherId)->get()
        ]);
    }

    /**
     * Okul türüne göre otomatik sınıflar oluştur
     */
    private function createDefaultClasses(School $school, string $schoolType)
    {
        $gradeLevels = $school->getGradeLevels();
        $branches = ['A', 'B', 'C', 'D'];

        foreach ($gradeLevels as $level) {
            foreach ($branches as $branch) {
                $className = $level['value'] === 0 ? "Hazırlık-{$branch}" : "{$level['value']}-{$branch}";
                
                // Sınıf oluştur
                $classRoom = \App\Models\ClassRoom::create([
                    'school_id' => $school->id,
                    'name' => $className,
                    'grade' => $level['value'],
                    'branch' => $branch,
                    'capacity' => 30,
                    'is_active' => true
                ]);
                
                // Her sınıf için otomatik derslik oluştur
                \App\Models\Classroom::create([
                    'school_id' => $school->id,
                    'name' => "{$className} Dersliği",
                    'code' => $className,
                    'type' => 'classroom',
                    'capacity' => 30,
                    'current_occupancy' => 0,
                    'is_active' => true
                ]);
            }
        }
    }
}
