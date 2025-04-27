<?php

namespace App\Models\Appointment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'vet_schedule',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
