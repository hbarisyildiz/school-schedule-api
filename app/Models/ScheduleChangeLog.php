<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleChangeLog extends Model
{
    public $timestamps = false; // Sadece created_at kullanıyoruz

    protected $fillable = [
        'school_id',
        'schedule_id',
        'user_id',
        'action',
        'old_data',
        'new_data',
        'reason',
        'ip_address'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * Okul ilişkisi
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Ders programı ilişkisi
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Değişikliği yapan kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Belirli bir okul için loglar
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Belirli bir program için loglar
     */
    public function scopeForSchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    /**
     * Belirli bir aksiyon için loglar
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Log kaydı oluştur (Helper method)
     */
    public static function logChange(
        int $scheduleId,
        string $action,
        ?array $oldData = null,
        ?array $newData = null,
        ?string $reason = null
    ) {
        return self::create([
            'school_id' => auth()->user()->school_id,
            'schedule_id' => $scheduleId,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_data' => $oldData,
            'new_data' => $newData,
            'reason' => $reason,
            'ip_address' => request()->ip()
        ]);
    }
}
