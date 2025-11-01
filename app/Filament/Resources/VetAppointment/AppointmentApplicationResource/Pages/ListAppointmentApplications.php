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

    // protected function getVetAppointmentTab(): array
    // {
    //      return [
    //         null => Tab::make('All')
    //                 ->badge(VetAppointment::count()),

    //         // 'Pending' => Tab::make()
    //         //                 ->badge(VetAppointment::AppointmentStatus('pending')->count())
    //         //                 ->modifyQueryUsing(fn ($query) => $query->AppointmentStatus('pending')),

    //         'Booked' => Tab::make()
    //                         ->badge(VetAppointment::AppointmentStatus('approved')->count())
    //                         ->modifyQueryUsing(fn ($query) => $query->AppointmentStatus('approved')),

    //         'Completed' => Tab::make()
    //                         ->badge(VetAppointment::AppointmentStatus('completed')->count())
    //                         ->modifyQueryUsing(fn ($query) => $query->AppointmentStatus('completed')),

    //         // 'Rejected' => Tab::make()
    //         //                  ->badge(VetAppointment::AppointmentStatus('rejected')->count())
    //         //                 ->query(fn ($query) => $query->AppointmentStatus('rejected')),


    //     ];
    // }

    protected function getVetAppointmentTab(): array
    {
        $user = auth()->user();

        // If user is super-admin, show all appointments
        if ($user->hasAnyRole(['super-admin','super_admin','secretary_vet'])) {
            return [
                null => Tab::make('All')
                    ->badge(VetAppointment::count()),

                'Booked' => Tab::make()
                    ->badge(VetAppointment::AppointmentStatus('approved')->count())
                    ->modifyQueryUsing(fn($query) => $query->AppointmentStatus('approved')),

                'Completed' => Tab::make()
                    ->badge(VetAppointment::AppointmentStatus('completed')->count())
                    ->modifyQueryUsing(fn($query) => $query->AppointmentStatus('completed')),
            ];
        }

        // For vet doctors, show only their assigned appointments
        $doctorId = $user->id;
        return [
            null => Tab::make('All')
                ->badge(VetAppointment::whereHas('categories', function ($q) use ($doctorId) {
                    $q->where('doctor_id', $doctorId);
                })->count()),

            'Booked' => Tab::make()
                ->badge(VetAppointment::whereHas('categories', function ($q) use ($doctorId) {
                    $q->where('doctor_id', $doctorId);
                })->AppointmentStatus('approved')->count())
                ->modifyQueryUsing(fn($query) => $query->AppointmentStatus('approved')
                    ->whereHas('categories', function ($q) use ($doctorId) {
                        $q->where('doctor_id', $doctorId);
                    })),

            'Completed' => Tab::make()
                ->badge(VetAppointment::whereHas('categories', function ($q) use ($doctorId) {
                    $q->where('doctor_id', $doctorId);
                })->AppointmentStatus('completed')->count())
                ->modifyQueryUsing(fn($query) => $query->AppointmentStatus('completed')
                    ->whereHas('categories', function ($q) use ($doctorId) {
                        $q->where('doctor_id', $doctorId);
                    })),
        ];
    }
}
