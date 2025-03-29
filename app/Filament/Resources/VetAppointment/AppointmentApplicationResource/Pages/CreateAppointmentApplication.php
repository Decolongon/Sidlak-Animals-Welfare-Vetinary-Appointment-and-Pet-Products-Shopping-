<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointmentApplication extends CreateRecord
{
    protected static string $resource = AppointmentApplicationResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
