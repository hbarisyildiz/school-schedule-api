<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = [
        'name',
        'city_id'
    ];

    /**
     * İlçenin bağlı olduğu şehir
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * İlçeye ait okullar
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
