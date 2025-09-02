<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointmentApplication extends CreateRecord
{
    protected static string $resource = AppointmentApplicationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['pet_age'] = $data['pet_age']. ' '. ucwords($data['pet_age_unit']);
        unset($data['pet_age_unit']);
        return $data;
    }

   
}
