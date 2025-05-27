<?php

namespace App\Models\Appointment;

use App\Models\User;
use Illuminate\Support\Carbon;
use Guava\Calendar\Contracts\Eventable;
use Illuminate\Database\Eloquent\Model;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetSchedule extends Model implements Eventable
{
    protected $fillable = [
        'user_id',
        'vet_schedule_open',
        'vet_schedule_close',
        'is_the_same_schedule',
        'num_customers',
    ];

    protected $casts = [
        'vet_schedule_open' => 'datetime',
        'vet_schedule_close' => 'datetime',
        'is_the_same_schedule' => 'boolean',
        'num_customers' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     public function toCalendarEvent(): CalendarEvent|array {

        return CalendarEvent::make($this)
            ->title('Schedule')
            ->start(Carbon::parse($this->vet_schedule_open)->timezone('Asia/Manila')->toIso8601String())
            ->end(Carbon::parse($this->vet_schedule_close)->timezone('Asia/Manila')->toIso8601String());
          
    }
}
