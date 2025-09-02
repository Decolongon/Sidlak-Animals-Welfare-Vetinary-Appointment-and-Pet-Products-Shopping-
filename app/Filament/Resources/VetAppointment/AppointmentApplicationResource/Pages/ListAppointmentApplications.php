<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Models\Appointment\Appointment as VetAppointment;
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
      return $this->getVetAppointmentTab();
    }

    protected function getVetAppointmentTab(): array
    {
         return [
            null => Tab::make('All')
                    ->badge(VetAppointment::count()),

            'Pending' => Tab::make()
                            ->badge(VetAppointment::AppointmentStatus('pending')->count())
                            ->modifyQueryUsing(fn ($query) => $query->AppointmentStatus('pending')),

            'Approved' => Tab::make()
                            ->badge(VetAppointment::AppointmentStatus('approved')->count())
                            ->modifyQueryUsing(fn ($query) => $query->AppointmentStatus('approved')),

            'Completed' => Tab::make()
                            ->badge(VetAppointment::AppointmentStatus('completed')->count())
                            ->modifyQueryUsing(fn ($query) => $query->AppointmentStatus('completed')),

            'Rejected' => Tab::make()
                             ->badge(VetAppointment::AppointmentStatus('rejected')->count())
                            ->query(fn ($query) => $query->AppointmentStatus('rejected')),
            
 
        ];
    }

    
    
}
