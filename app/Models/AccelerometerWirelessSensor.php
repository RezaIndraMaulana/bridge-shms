<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccelerometerWirelessSensor extends Model
{
    protected $fillable = [
        'bridge_id',
        'brand_model',
        'mac_address',
        'name',
        'type',
    ];

    public function bridge(): BelongsTo
    {
        return $this->belongsTo(Bridge::class);
    }

    public function sensorData(): HasMany
    {
        return $this->hasMany(AccelerometerSensorData::class);
    }

    public function latestData()
    {
        return $this->hasOne(AccelerometerSensorData::class)->latestOfMany('recorded_at');
    }
}
