<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Models\Appointment\Appointment;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class ListAppointmentApplications extends ListRecords
{
    protected static string $resource = AppointmentApplicationResource::class;
   

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-s-plus-circle')->label('New Application'),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
                    ->badge(Appointment::count()),

            'Pending' => Tab::make()
                            ->badge(Appointment::where('appointment_status','pending')->count())
                            ->query(fn ($query) => $query->where('appointment_status', 'pending')),

            'Approved' => Tab::make()
                            ->badge(Appointment::where('appointment_status','approved')->count())
                            ->query(fn ($query) => $query->where('appointment_status', 'approved')),

            'Rejected' => Tab::make()
                             ->badge(Appointment::where('appointment_status','rejected')->count())
                            ->query(fn ($query) => $query->where('appointment_status', 'rejected')),
 
        ];
    }

    
    
}
