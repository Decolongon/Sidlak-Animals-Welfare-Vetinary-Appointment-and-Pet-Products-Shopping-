<?php

namespace App\Models\Appointment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'vet_schedule_open',
        'vet_schedule_close',
        'is_the_same_schedule',
    ];

    protected $casts = [
        'vet_schedule_open' => 'datetime',
        'vet_schedule_close' => 'datetime',
        'is_the_same_schedule' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
