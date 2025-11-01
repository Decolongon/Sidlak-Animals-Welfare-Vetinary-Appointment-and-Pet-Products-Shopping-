<?php

namespace App\Filament\Resources\VetAppointment\DoctorScheduleResource\Pages;

use App\Filament\Resources\VetAppointment\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorSchedule extends EditRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
