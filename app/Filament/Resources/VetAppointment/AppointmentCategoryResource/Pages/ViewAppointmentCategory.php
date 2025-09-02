<?php

namespace App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\VetAppointment\AppointmentCategoryResource;

class ViewAppointmentCategory extends ViewRecord
{
    protected static string $resource = AppointmentCategoryResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var AppointmentCategory */
        $record = $this->getRecord();

        return ucwords($record->appoint_cat_name);
    }

    // public function hasCombinedRelationManagerTabsWithContent(): bool
    // {
    //     return true;
    // }
}
