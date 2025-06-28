<?php

namespace App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointmentCategory extends EditRecord
{
    protected static string $resource = AppointmentCategoryResource::class;

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
