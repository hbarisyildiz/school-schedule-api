<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassDailySchedule extends Model
{
    protected $fillable = [
        'class_id',
        'day',
        'lesson_count'
    ];

    protected $casts = [
        'lesson_count' => 'integer'
    ];

    /**
     * Sınıf ilişkisi
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
