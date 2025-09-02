<?php

namespace App\Models\Appointment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'service_id',
        'days',
        'start_time',
        'end_time',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'days' => 'array',
        'effective_from' => 'date',
        'effective_to' => 'date',

    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(AppointmentCategory::class, 'service_id');
    }
}
