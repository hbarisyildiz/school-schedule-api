<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = "classrooms";
    
    protected $fillable = [
        "school_id",
        "name",
        "code",
        "type",
        "equipment",
        "description",
        "is_active"
    ];

    protected $casts = [
        "equipment" => "array",
        "is_active" => "boolean"
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, "classroom_id");
    }

    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where("school_id", $schoolId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where("type", $type);
    }

    public function isAvailable()
    {
        return $this->is_active;
    }
}
