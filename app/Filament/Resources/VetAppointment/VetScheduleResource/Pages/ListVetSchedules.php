<?php

namespace App\Filament\Resources\VetAppointment\VetScheduleResource\Pages;

use App\Filament\Resources\VetAppointment\VetScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVetSchedules extends ListRecords
{
    protected static string $resource = VetScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-s-plus-circle')->label('New Vetinary Schedule'),
        ];
    }
}
