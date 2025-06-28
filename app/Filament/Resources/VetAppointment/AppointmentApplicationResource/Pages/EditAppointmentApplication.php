<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class EditAppointmentApplication extends EditRecord
{
    protected static string $resource = AppointmentApplicationResource::class;

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

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['pet_age'] = $data['pet_age']. ' '. $data['pet_age_unit'];
    //     return $data;
    // }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['pet_age'])) {
            // Extract number and unit
            preg_match('/(?<age>\d+(?:\.\d+)?)\s*(?<unit>years old|months)?/i', $data['pet_age'], $matches);
            
            $data['pet_age'] = $matches['age'] ?? null;
            $data['pet_age_unit'] = strtolower($matches['unit'] ?? 'years'); // default to years if missing
        }

        return $data;
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['pet_age'] = $data['pet_age']. ' '. ucwords($data['pet_age_unit']);
        unset($data['pet_age_unit']);

        return $data;
    }

   

     
}
