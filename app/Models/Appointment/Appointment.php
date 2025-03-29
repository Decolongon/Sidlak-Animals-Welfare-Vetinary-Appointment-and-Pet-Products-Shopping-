<?php

namespace App\Models\Appointment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
   
    protected $fillable = [
        'user_id',
        'appointment_category_id',
        'pet_name',
        'pet_type',
        'pet_breed',
        'pet_gender',
        'pet_weight',
        'pet_age',
        'isPetVaccinated',
        'appointment_status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'isPetVaccinated' => 'boolean',
    ];

    // public function category():BelongsTo
    // {
    //     return $this->belongsTo(AppointmentCategory::class,'appointment_category_id');
    // }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(AppointmentCategory::class, 'appointment_appointment_category', 'appointment_id', 'appointment_category_id')->withTimestamps();
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
