<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'deleted_at' => 'datetime'
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
     * Okunmamış bildirimler
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Okunmuş bildirimler
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Belirli bir kullanıcının bildirimleri
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Belirli bir tip bildirimler
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Bildirim okundu mu?
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Bildirimi okundu olarak işaretle
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Bildirimi okunmadı olarak işaretle
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }
}
