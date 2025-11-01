<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Appointment\Appointment;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Appointment\DoctorSchedule;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AvailableSlotVetWidget extends BaseWidget
{
    use HasWidgetShield;
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Appointment Slots';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DoctorSchedule::query()
                    ->with(['doctor'])
                    ->where('effective_to', '>=', now()->startOfDay())
                    ->orderBy('effective_from')
            )
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor'),

                Tables\Columns\TextColumn::make('days')
                    ->label('Available Days')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->map(fn($day) => ucfirst($day))->join(', ');
                        }
                        return ucfirst($state);
                    })
                    ->badge()
                     ->wrap()
                    ->html()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('effective_from')
                    ->label('Valid From')
                    ->date('M d, Y'),
       

                Tables\Columns\TextColumn::make('effective_to')
                    ->label('Valid To')
                    ->date('M d, Y'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('g:i A'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('g:i A'),

                Tables\Columns\TextColumn::make('available_slots_today')
                    ->label('Available Today')
                    ->getStateUsing(function (DoctorSchedule $record) {
                        return $this->getAvailableSlotsForDate($record, now()->format('Y-m-d'));
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('available_slots_tomorrow')
                    ->label('Available Tomorrow')
                    ->getStateUsing(function (DoctorSchedule $record) {
                        return $this->getAvailableSlotsForDate($record, now()->addDay()->format('Y-m-d'));
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'warning'),

                // Tables\Columns\TextColumn::make('next_7_days_slots')
                //     ->label('Next 7 Days')
                //     ->getStateUsing(function (DoctorSchedule $record) {
                //         $totalSlots = 0;
                //         for ($i = 0; $i < 7; $i++) {
                //             $date = now()->addDays($i)->format('Y-m-d');
                //             $totalSlots += (int) $this->getAvailableSlotsForDate($record, $date);
                //         }
                //         return $totalSlots;
                //     })
                //     ->badge()
                //     ->color(fn ($state) => $state > 0 ? 'primary' : 'gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_slots')
                    ->label('View All Slots')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (DoctorSchedule $record) => "Available Slots for Dr. {$record->doctor->name}")
                    ->modalDescription(fn (DoctorSchedule $record) => "Showing all available slots")
                    ->form(fn (DoctorSchedule $record) => $this->getSlotFormSchema($record))
                    ->modalWidth('3xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->action(fn () => null),
            ])
            ->modifyQueryUsing(function () {
                if (auth()->user()->hasAnyRole(['super_admin', 'super-admin','secretary_vet'])) {
                    return DoctorSchedule::query();
                }
                return DoctorSchedule::query()->where('doctor_id', auth()->user()->id);
            })
            ->emptyStateHeading('No schedules available')
            ->emptyStateDescription('Create doctor schedules to see available slots.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    protected function getSlotFormSchema(DoctorSchedule $record): array
    {
        $slotsByDate = $this->getSlotsGroupedByDate($record);
        
        if (empty($slotsByDate)) {
            return [
                \Filament\Forms\Components\Placeholder::make('no_slots')
                    ->content('No available slots found.')
                    ->extraAttributes(['class' => 'text-center py-8 text-gray-500'])
            ];
        }

        // Create tabs for each day
        $tabs = [];
        
        foreach ($slotsByDate as $date => $daySlots) {
            $dateObj = Carbon::parse($date);
            $tabLabel = $dateObj->format('D, M d');
            
            // Count available and booked slots
            $availableCount = collect($daySlots)->where('is_available', true)->count();
            $bookedCount = collect($daySlots)->where('is_booked', true)->count();
            
            // Create slot cards for this day - using 2 columns
            $slotCards = [];
            foreach ($daySlots as $slot) {
                // Get booking user info if slot is booked
                $bookedBy = '';
                if ($slot['is_booked'] && $slot['appointment']) {
                    $bookedBy = ucwords($slot['appointment']['user_name']) ?? 'Unknown User';
                }

                $slotCards[] = \Filament\Forms\Components\Card::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('time_' . $slot['time'])
                            ->label($slot['display_time'])
                            ->content($slot['is_available'] ? 'Available' : "Booked by: {$bookedBy}")
                            ->extraAttributes([
                                'class' => $slot['is_available'] 
                                    ? 'text-success-600 bg-success-50 border-success-200 p-4 rounded-lg text-center font-medium' 
                                    : 'text-danger-600 bg-danger-50 border-danger-200 p-4 rounded-lg text-center font-medium'
                            ])
                    ])
                    ->columnSpan(1);
            }

            $tabs[] = \Filament\Forms\Components\Tabs\Tab::make($tabLabel)
                ->badge($availableCount)
                ->badgeColor($availableCount > 0 ? 'success' : 'danger')
                ->schema([
                    \Filament\Forms\Components\Section::make()
                        ->description("{$availableCount} available, {$bookedCount} booked")
                        ->schema([
                            \Filament\Forms\Components\Grid::make()
                                ->columns(2) // Changed to 2 columns as requested
                                ->schema($slotCards)
                        ])
                        ->collapsible(false),
                ]);
        }

        return [
            \Filament\Forms\Components\Tabs::make('dates')
                ->tabs($tabs)
                ->persistTabInQueryString()
                ->columnSpanFull(),
        ];
    }

    protected function getSlotsGroupedByDate(DoctorSchedule $schedule): array
    {
        $slotsByDate = [];
        $startDate = now()->startOfDay();
        $endDate = Carbon::parse($schedule->effective_to)->endOfDay(); // Show all slots until schedule end date

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dayName = strtolower($date->format('l'));
            $dateString = $date->format('Y-m-d');

            // Check if schedule is valid for this date
            if (!$this->isScheduleValidForDate($schedule, $date)) {
                continue;
            }

            // Check if day matches schedule
            if (!$this->isDayInSchedule($schedule, $dayName)) {
                continue;
            }

            // Generate time slots
            $daySlots = $this->generateTimeSlots($schedule, $dateString);
            
            if (!empty($daySlots)) {
                $slotsByDate[$dateString] = $daySlots;
            }
        }

        return $slotsByDate;
    }

    protected function isScheduleValidForDate(DoctorSchedule $schedule, Carbon $date): bool
    {
        return $date->between(
            Carbon::parse($schedule->effective_from)->startOfDay(),
            Carbon::parse($schedule->effective_to)->endOfDay()
        );
    }

    protected function isDayInSchedule(DoctorSchedule $schedule, string $dayName): bool
    {
        $days = is_array($schedule->days) ? $schedule->days : [$schedule->days];
        $scheduleDays = array_map('strtolower', $days);
        
        return in_array($dayName, $scheduleDays);
    }

    protected function generateTimeSlots(DoctorSchedule $schedule, string $dateString): array
    {
        $daySlots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        $currentTime = $startTime->copy();
        while ($currentTime < $endTime) {
            $timeString = $currentTime->format('H:i:s');
            $displayTime = $currentTime->format('g:i A');

            // Check if this time slot is booked and get appointment details
            $appointment = Appointment::with('user')
                ->whereDate('appoint_sched', $dateString)
                ->whereTime('appoint_sched', $timeString)
                ->first();

            $isBooked = !is_null($appointment);
            
            $slotData = [
                'time' => $timeString,
                'display_time' => $displayTime,
                'is_available' => !$isBooked,
                'is_booked' => $isBooked,
            ];

            // Add appointment details if booked
            if ($isBooked && $appointment) {
                $slotData['appointment'] = [
                    'user_name' => $appointment->user->name ?? 'Unknown User',
                    'pet_name' => $appointment->pet_name ?? 'Unknown Pet',
                ];
            }

            $daySlots[] = $slotData;

            $currentTime->addHour();
        }

        return $daySlots;
    }

    protected function getAvailableSlotsForDate(DoctorSchedule $schedule, string $date): string
    {
        $dateObj = Carbon::parse($date);
        $dayName = strtolower($dateObj->format('l'));

        // Check if schedule is valid for this date
        if (!$this->isScheduleValidForDate($schedule, $dateObj)) {
            return '0';
        }

        // Check if day matches schedule
        if (!$this->isDayInSchedule($schedule, $dayName)) {
            return '0';
        }

        // Generate slots and count available ones
        $slots = $this->generateTimeSlots($schedule, $date);
        $availableCount = collect($slots)->where('is_available', true)->count();

        return (string) $availableCount;
    }

    // Method to get formatted slots for display (similar to your desired output)
    public static function getFormattedWeeklySlots(): array
    {
        $formattedSlots = [];
        $startDate = now()->startOfDay();
        
        // Get all active schedules
        $schedules = DoctorSchedule::with(['doctor'])
            ->where('effective_to', '>=', now()->startOfDay())
            ->get();

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            $dayName = $date->format('D, M d');
            
            $daySlots = [];
            $totalAvailable = 0;
            
            foreach ($schedules as $schedule) {
                $instance = new self();
                
                if ($instance->isScheduleValidForDate($schedule, $date) && 
                    $instance->isDayInSchedule($schedule, strtolower($date->format('l')))) {
                    
                    $slots = $instance->generateTimeSlots($schedule, $dateString);
                    $availableSlots = collect($slots)->where('is_available', true)->count();
                    $totalAvailable += $availableSlots;
                    
                    // Add doctor-specific slots
                    foreach ($slots as $slot) {
                        if ($slot['is_available']) {
                            $daySlots[] = [
                                'time' => $slot['display_time'],
                                'doctor' => $schedule->doctor->name,
                                'schedule_id' => $schedule->id,
                            ];
                        }
                    }
                }
            }
            
            $formattedSlots[] = [
                'day' => $dayName,
                'date' => $dateString,
                'total_available' => $totalAvailable,
                'slots' => $daySlots,
            ];
        }
        
        return $formattedSlots;
    }
}