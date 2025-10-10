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
        'last_activity_at'
    ];

    protected $casts = [
        'subscription_starts_at' => 'date',
        'subscription_ends_at' => 'date',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean'
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
}
