<?php

namespace App\Filament\Resources\VetAppointment\DoctorScheduleResource\Pages;

use App\Filament\Resources\VetAppointment\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctorSchedules extends ListRecords
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


}
