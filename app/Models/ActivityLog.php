<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false; // Sadece created_at kullanıyoruz

    protected $fillable = [
        'school_id',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
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
     * Kullanıcı ilişkisi
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
     * Belirli bir kullanıcı için loglar
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Belirli bir aksiyon için loglar
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Belirli bir entity için loglar
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                     ->where('entity_id', $entityId);
    }

    /**
     * Son N günün logları
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Log kaydı oluştur (Helper method)
     */
    public static function log(
        string $action,
        ?int $schoolId = null,
        ?int $userId = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null
    ) {
        return self::create([
            'school_id' => $schoolId ?? auth()->user()?->school_id,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
