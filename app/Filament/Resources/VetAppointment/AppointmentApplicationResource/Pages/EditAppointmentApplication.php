<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class EditAppointmentApplication extends EditRecord
{
    protected static string $resource = AppointmentApplicationResource::class;
    protected static ?string $navigationLabel = 'Edit Appointment';

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
        $data['pet_age'] = $data['pet_age'] . ' ' . ucwords($data['pet_age_unit']);
        unset($data['pet_age_unit']);

        return $data;
    }



    // protected function afterSave(): void
    // {
    //     $data = $this->form->getState();
    //     $record = $this->getRecord();

    //     // Handle category approvals
    //     if (isset($data['categories'])) {
    //         $syncData = [];
    //         foreach ($data['categories'] as $categoryId) {
    //             $isApproved = $data['appointment_status'] === 'approved' &&
    //                 (isset($data['approved_categories']) ?
    //                     in_array($categoryId, $data['approved_categories']) :
    //                     true);

    //             $syncData[$categoryId] = ['is_approved' => $isApproved];
    //         }

    //         $record->categories()->sync($syncData);
    //     } else {
    //         // If no categories selected, reset approvals
    //         $syncData = [];
    //         foreach ($record->categories as $category) {
    //             $syncData[$category->id] = ['is_approved' => false];
    //         }
    //         $record->categories()->sync($syncData);
    //     }
    // }
}
