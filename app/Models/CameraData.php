<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CameraData extends Model
{
    public $timestamps = false;

    // Composite primary key — no single auto-increment
    protected $primaryKey = null;
    public $incrementing = false;

    protected $table = 'camera_data';

    protected $fillable = [
        'camera_id',
        'recorded_at',
        'truck_detected',
        'image_url',
    ];

    protected $casts = [
        'recorded_at'    => 'datetime',
        'truck_detected' => 'boolean',
    ];

    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }
}
