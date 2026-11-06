<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccelerometerSensorData extends Model
{
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'accelerometer_wireless_sensor_id',
        'recorded_at',
        'frequency_hz',
        'amplitude',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(AccelerometerWirelessSensor::class, 'accelerometer_wireless_sensor_id');
    }
}
