<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolBreakDuration extends Model
{
    protected $fillable = [
        'school_id',
        'after_period',
        'duration',
        'is_lunch_break'
    ];

    protected $casts = [
        'is_lunch_break' => 'boolean',
        'after_period' => 'integer',
        'duration' => 'integer'
    ];

    /**
     * Okul ilişkisi
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
