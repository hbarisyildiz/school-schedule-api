<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'level',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Bu role sahip kullanıcılar
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Aktif roller
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * İzin kontrol
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Yetki seviyesi kontrol
     */
    public function isHigherThan(Role $role): bool
    {
        return $this->level < $role->level;
    }
}
