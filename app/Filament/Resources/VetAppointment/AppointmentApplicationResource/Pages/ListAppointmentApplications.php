<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class ListAppointmentApplications extends ListRecords
{
    protected static string $resource = AppointmentApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus')->label('New Application'),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Pending' => Tab::make()->query(fn ($query) => $query->where('appointment_status', 'pending')),
            'Approved' => Tab::make()->query(fn ($query) => $query->where('appointment_status', 'approved')),
            'Rejected' => Tab::make()->query(fn ($query) => $query->where('appointment_status', 'rejected')),
 
        ];
    }

}
