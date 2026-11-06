<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'city_id', 'contact_email'];

    /** Bridges that belong to this company */
    public function bridges(): HasMany
    {
        return $this->hasMany(Bridge::class);
    }

    /** Users (PICs/staff) assigned to this company */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
