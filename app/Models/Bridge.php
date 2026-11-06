<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bridge extends Model
{
    protected $fillable = [
        'city_id',
        'company_id',
        'name',
        'latitude',
        'longitude',
        'rating',
        'photo_url',
        'maintenance_status',
        'support_type',
        'box_beam_length',
        'box_beam_inside_width',
        'box_beam_outside_width',
        'box_beam_inside_height',
        'box_beam_outside_height',
        'number_of_box_beams',
        'number_of_diaphragms_per_box_beam',
        'thickness_at_diaphragms',
        'thickness_of_box_beam_end',
        'thickness_of_wearing_surface',
        'compressive_strength_28_days',
        'unit_weight_of_concrete',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(AccelerometerWirelessSensor::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /** Company that owns this bridge */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /** PICs assigned to this bridge (users with role pic) */
    public function pics()
    {
        return $this->hasMany(User::class)->where('role', 'pic');
    }
}
