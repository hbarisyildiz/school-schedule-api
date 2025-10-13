<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'city_id',
        'district_id',
        'logo',
        'website',
        'subscription_plan_id',
        'subscription_starts_at',
        'subscription_ends_at',
        'subscription_status',
        'current_teachers',
        'current_students',
        'current_classes',
        'is_active',
        'last_activity_at',
        'class_days',
        'lesson_duration',
        'break_durations',
        'school_hours',
        'weekly_lesson_count',
        'schedule_settings',
        'daily_lesson_counts',
        'class_daily_lesson_counts'
    ];

    protected $casts = [
        'subscription_starts_at' => 'date',
        'subscription_ends_at' => 'date',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
        'class_days' => 'array',
        'break_durations' => 'array',
        'school_hours' => 'array',
        'schedule_settings' => 'array',
        'daily_lesson_counts' => 'array',
        'class_daily_lesson_counts' => 'array'
    ];

    /**
     * Abonelik planı ilişkisi
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Şehir ilişkisi
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * İlçe ilişkisi
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Kullanıcılar ilişkisi
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Öğretmenler
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', function($query) {
            $query->where('name', 'teacher');
        });
    }

    /**
     * Öğrenciler
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', function($query) {
            $query->where('name', 'student');
        });
    }

    /**
     * Dersler ilişkisi
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Sınıflar ilişkisi
     */
    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    /**
     * Ders programları ilişkisi
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Aktif okullar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Abonelik durumu kontrol
     */
    public function isSubscriptionActive(): bool
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_ends_at > now();
    }

    /**
     * Plan limiti kontrol
     */
    public function canAddTeacher(): bool
    {
        if ($this->subscriptionPlan->hasUnlimitedTeachers()) {
            return true;
        }
        return $this->current_teachers < $this->subscriptionPlan->max_teachers;
    }

    public function canAddStudent(): bool
    {
        if ($this->subscriptionPlan->hasUnlimitedStudents()) {
            return true;
        }
        return $this->current_students < $this->subscriptionPlan->max_students;
    }

    public function canAddClass(): bool
    {
        if ($this->subscriptionPlan->hasUnlimitedClasses()) {
            return true;
        }
        return $this->current_classes < $this->subscriptionPlan->max_classes;
    }

    /**
     * Okul ayarları için varsayılan değerler
     */
    public function getDefaultClassDays(): array
    {
        return $this->class_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    }

    public function getDefaultLessonDuration(): int
    {
        return $this->lesson_duration ?? 40;
    }

    public function getDefaultBreakDurations(): array
    {
        return $this->break_durations ?? [
            'small_break' => 10,
            'lunch_break' => 20
        ];
    }

    public function getDefaultSchoolHours(): array
    {
        return $this->school_hours ?? [
            'start_time' => '08:00',
            'end_time' => '16:00'
        ];
    }

    public function getDefaultWeeklyLessonCount(): int
    {
        return $this->weekly_lesson_count ?? 30;
    }

    public function getDefaultScheduleSettings(): array
    {
        return $this->schedule_settings ?? [
            'allow_teacher_conflicts' => false,
            'allow_classroom_conflicts' => false,
            'max_lessons_per_day' => 8,
            'min_lessons_per_day' => 4
        ];
    }

    /**
     * Ders günlerini Türkçe olarak döndür
     */
    public function getClassDaysInTurkish(): array
    {
        $days = [
            'monday' => 'Pazartesi',
            'tuesday' => 'Salı',
            'wednesday' => 'Çarşamba',
            'thursday' => 'Perşembe',
            'friday' => 'Cuma',
            'saturday' => 'Cumartesi',
            'sunday' => 'Pazar'
        ];

        $classDays = $this->getDefaultClassDays();
        return array_map(function($day) use ($days) {
            return $days[$day] ?? $day;
        }, $classDays);
    }

    /**
     * Günlük ders sayılarını getir
     */
    public function getDailyLessonCounts(): array
    {
        return $this->daily_lesson_counts ?? [];
    }

    /**
     * Belirli bir günün ders sayısını getir
     */
    public function getLessonCountForDay(string $day): int
    {
        $counts = $this->getDailyLessonCounts();
        return $counts[$day] ?? $this->schedule_settings['max_lessons_per_day'] ?? 8;
    }

    /**
     * Sınıf bazlı günlük ders sayılarını getir
     */
    public function getClassDailyLessonCounts(): array
    {
        return $this->class_daily_lesson_counts ?? [];
    }

    /**
     * Belirli bir sınıfın belirli bir gündeki ders sayısını getir
     */
    public function getLessonCountForClassAndDay(string $className, string $day): int
    {
        $classCounts = $this->getClassDailyLessonCounts();
        
        if (isset($classCounts[$className][$day])) {
            return $classCounts[$className][$day];
        }
        
        return $this->getLessonCountForDay($day);
    }

    /**
     * Belirli bir sınıfın tüm günlerdeki ders sayılarını getir
     */
    public function getLessonCountsForClass(string $className): array
    {
        $classCounts = $this->getClassDailyLessonCounts();
        
        if (isset($classCounts[$className])) {
            return $classCounts[$className];
        }
        
        return $this->getDailyLessonCounts();
    }
}
