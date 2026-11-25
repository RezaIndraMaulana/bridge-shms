<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SinkNode extends Model
{
    protected $fillable = [
        'bridge_id',
        'ip_address',
        'status',
    ];

    public function bridge(): BelongsTo
    {
        return $this->belongsTo(Bridge::class);
    }

    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class);
    }

    public function accelerometerSensors(): HasMany
    {
        return $this->hasMany(AccelerometerWirelessSensor::class);
    }
}
