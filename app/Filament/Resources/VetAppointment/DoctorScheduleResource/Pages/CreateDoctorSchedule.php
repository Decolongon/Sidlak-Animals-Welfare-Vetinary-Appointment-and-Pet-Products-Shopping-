<?php

namespace App\Filament\Resources\VetAppointment\DoctorScheduleResource\Pages;

use App\Filament\Resources\VetAppointment\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctorSchedule extends CreateRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
