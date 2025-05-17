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
            //->chart($this->getChartVet())
            ->url(route('filament.admin.resources.vet-appointment.appointment-applications.index', [
                'tableFilters[appointment_status][value]' => 'pending'
            ]))
            ->color('warning'),
        ];
    }

    
}
