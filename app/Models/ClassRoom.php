<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    protected $table = 'classes'; // Veritabanında 'classes' tablosu

    protected $fillable = [
        'school_id',
        'name',
        'grade',
        'branch',
        'capacity',
        'current_students',
        'classroom',
        'class_teacher_id',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Okul ilişkisi
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Sınıf öğretmeni
     */
    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    /**
     * Bu sınıfın öğrencileri
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'class_id')->whereHas('role', function($query) {
            $query->where('name', 'student');
        });
    }

    /**
     * Ders programları
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    /**
     * Aktif sınıflar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Okul bazlı filtreleme
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Sınıf dolu mu kontrol
     */
    public function isFull(): bool
    {
        return $this->current_students >= $this->capacity;
    }

    /**
     * Öğrenci eklenebilir mi
     */
    public function canAddStudent(): bool
    {
        return !$this->isFull();
    }
}
