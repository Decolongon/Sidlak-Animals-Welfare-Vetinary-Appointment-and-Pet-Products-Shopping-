<?php

namespace App\Models\Appointment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment\DoctorSchedule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AppointmentCategory extends Model
{
    protected $table = 'appointment_categories';

    protected $fillable = [
        'appoint_cat_name',
        'appoint_cat_slug',
        'appoint_cat_description',
        'price',
        'doctor_id',
        'img'
    ];

    // public function appointments(): HasMany
    // {
    //     return $this->hasMany(Appointment::class);
    // }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_appointment_category', 'appointment_category_id', 'appointment_id')->withTimestamps();
    }

    public function doctorschedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class,'service_id');
    }

    public function doctor():BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function getRouteKeyName(): string
    {
         return 'appoint_cat_slug';
    }
}
