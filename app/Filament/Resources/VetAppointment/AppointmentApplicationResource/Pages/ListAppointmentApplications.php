<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentApplications extends ListRecords
{
    protected static string $resource = AppointmentApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus')->label('New Application'),
        ];
    }
}
