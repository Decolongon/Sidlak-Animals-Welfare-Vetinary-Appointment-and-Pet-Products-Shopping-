<?php

namespace App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ContentTabPosition;
use App\Filament\Resources\VetAppointment\AppointmentCategoryResource;

class EditAppointmentCategory extends EditRecord
{
    protected static string $resource = AppointmentCategoryResource::class;
    protected static ?string $title = 'Edit Service';

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

    // public function hasCombinedRelationManagerTabsWithContent(): bool
    // {
    //     return true;
    // }

    // public function getContentTabPosition(): ?ContentTabPosition
    // {
    //     return ContentTabPosition::Before;
    // }
   
}
