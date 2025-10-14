<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectTemplate extends Model
{
    protected $fillable = [
        'school_type',
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Okul türüne göre ders şablonlarını getir
     */
    public static function getBySchoolType($schoolType)
    {
        return self::where('school_type', $schoolType)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}

