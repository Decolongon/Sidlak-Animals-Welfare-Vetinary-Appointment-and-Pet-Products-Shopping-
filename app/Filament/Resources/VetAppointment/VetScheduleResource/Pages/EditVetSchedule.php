<?php

namespace App\Filament\Resources\VetAppointment\VetScheduleResource\Pages;

use App\Filament\Resources\VetAppointment\VetScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVetSchedule extends EditRecord
{
    protected static string $resource = VetScheduleResource::class;

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
