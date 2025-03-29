<?php

namespace App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointmentCategory extends CreateRecord
{
    protected static string $resource = AppointmentCategoryResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
