<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camera extends Model
{
    protected $fillable = [
        'sink_node_id',
        'brand_model',
        'mac_address',
    ];

    public function sinkNode(): BelongsTo
    {
        return $this->belongsTo(SinkNode::class);
    }

    public function cameraData(): HasMany
    {
        return $this->hasMany(CameraData::class);
    }

    public function latestData()
    {
        return $this->hasOne(CameraData::class)->latestOfMany('recorded_at');
    }
}
