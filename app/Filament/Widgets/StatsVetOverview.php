<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use App\Models\Appointment\Appointment;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsVetOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Book Appointments', Appointment::count())
                ->descriptionIcon('heroicon-o-rectangle-stack', IconPosition::Before)
                ->description('Total Appointments')
                ->chart([8, 12, 16, 20, 24, 28, 32, 36, 40, 45, 50, 55])
                ->url(route('filament.admin.resources.vet-appointment.appointment-applications.index'))
                ->color('success'),

            Stat::make('Pending Book Appointment', Appointment::AppointmentStatus('pending')->count())
                ->descriptionIcon('heroicon-o-clock', IconPosition::Before)
                ->description('Pending Appointments')
                ->chart([5, 8, 6, 9, 7, 10, 8, 11, 9, 12, 10, 13])
                ->url(route('filament.admin.resources.vet-appointment.appointment-applications.index', ['activeTab' => 'Pending']))
                ->color('warning'),

            Stat::make('Approved Book Appointment', Appointment::AppointmentStatus('approved')->count())
                ->descriptionIcon('heroicon-o-hand-thumb-up', IconPosition::Before)
                ->description('Approved Appointments')
                ->chart([3, 5, 8, 12, 15, 18, 22, 26, 30, 34, 38, 42])
                ->url(route('filament.admin.resources.vet-appointment.appointment-applications.index', ['activeTab' => 'Approved']))
                ->color('success'),

            Stat::make('Completed Appointments',Appointment::AppointmentStatus('completed')->count())
                ->descriptionIcon('heroicon-o-check-circle', IconPosition::Before)
                ->description('Completed Appointments')
                ->chart([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])
                ->url(route('filament.admin.resources.vet-appointment.appointment-applications.index', ['activeTab' => 'Completed']))
                ->color('info'),

            Stat::make('Rejected Book Appointment', Appointment::AppointmentStatus('rejected')->count())
                ->descriptionIcon('heroicon-o-x-circle', IconPosition::Before)
                ->description('Rejected Appointments')
                ->chart([2, 1, 3, 2, 1, 4, 2, 3, 1, 2, 3, 2])
                ->url(route('filament.admin.resources.vet-appointment.appointment-applications.index', ['activeTab' => 'Rejected']))
                ->color('danger'),
        ];
    }



    // protected function getChartAppointment(): array
    // {
    //     return [3, 1, 5, 2, 7, 3, 9, 8, 10];
    // }


    
    
}
