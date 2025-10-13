<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherDailySchedule extends Model
{
    protected $fillable = [
        'teacher_id',
        'day',
        'lesson_count'
    ];

    protected $casts = [
        'lesson_count' => 'integer'
    ];

    /**
     * Öğretmen ilişkisi
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
