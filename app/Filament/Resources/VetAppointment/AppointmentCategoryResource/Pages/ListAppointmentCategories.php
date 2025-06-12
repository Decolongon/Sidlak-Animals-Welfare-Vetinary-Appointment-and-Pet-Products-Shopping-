<?php

namespace App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;

use App\Filament\Resources\VetAppointment\AppointmentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentCategories extends ListRecords
{
    protected static string $resource = AppointmentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Create Service')->icon('heroicon-s-plus-circle'),
        ];
    }
}
