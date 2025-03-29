<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class ViewRecords extends ViewRecord
{
    protected static string $resource = AppointmentApplicationResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var AppointmentApplication */
        $record = $this->getRecord();

        return ucwords($record->user->name) . ' - ' . $record->email . ' Pet Owner'; 
    }
}
