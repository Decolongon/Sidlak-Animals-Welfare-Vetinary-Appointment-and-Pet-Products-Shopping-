<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointmentApplication extends EditRecord
{
    protected static string $resource = AppointmentApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
