<?php

namespace App\Models\Appointment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AppointmentCategory extends Model
{
    protected $table = 'appointment_categories';

    protected $fillable = [
        'appoint_cat_name',
        'appoint_cat_slug',
        'appoint_cat_description',
    ];

    // public function appointments(): HasMany
    // {
    //     return $this->hasMany(Appointment::class);
    // }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_appointment_category', 'appointment_category_id', 'appointment_id')->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
         return 'appoint_cat_slug';
    }
}
