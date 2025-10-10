<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug', 
        'description',
        'monthly_price',
        'yearly_price',
        'max_teachers',
        'max_students', 
        'max_classes',
        'features',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Okullar ilişkisi
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    /**
     * Aktif planlar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Plan limitleri kontrol
     */
    public function hasUnlimitedTeachers(): bool
    {
        return is_null($this->max_teachers);
    }

    public function hasUnlimitedStudents(): bool
    {
        return is_null($this->max_students);
    }

    public function hasUnlimitedClasses(): bool
    {
        return is_null($this->max_classes);
    }
}
