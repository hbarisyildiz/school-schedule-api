<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleConflict extends Model
{
    public $timestamps = false; // Sadece created_at kullanıyoruz

    protected $fillable = [
        'school_id',
        'schedule_id',
        'conflict_type',
        'conflicting_schedule_id',
        'severity',
        'description',
        'resolved_at',
        'resolved_by'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
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
     * Ana ders programı
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Çakışan ders programı
     */
    public function conflictingSchedule()
    {
        return $this->belongsTo(Schedule::class, 'conflicting_schedule_id');
    }

    /**
     * Çözümleyen kullanıcı
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Çözülmemiş çakışmalar
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Çözülmüş çakışmalar
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Belirli bir okul için çakışmalar
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Belirli bir severity için
     */
    public function scopeOfSeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Error severity çakışmalar
     */
    public function scopeErrors($query)
    {
        return $query->where('severity', 'error');
    }

    /**
     * Warning severity çakışmalar
     */
    public function scopeWarnings($query)
    {
        return $query->where('severity', 'warning');
    }

    /**
     * Çakışma çözüldü mü?
     */
    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    /**
     * Çakışmayı çözülmüş olarak işaretle
     */
    public function resolve(?int $userId = null)
    {
        $this->update([
            'resolved_at' => now(),
            'resolved_by' => $userId ?? auth()->id()
        ]);
    }
}
