<?php

namespace App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use App\Enums\PaymentStatusEnum;
use App\Enums\AppointmentStatusEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class ViewRecords extends ViewRecord
{
    protected static string $resource = AppointmentApplicationResource::class;
    protected static ?string $navigationLabel = 'View Appointment';

    public function getTitle(): string | Htmlable
    {
        /** @var AppointmentApplication */
        $record = $this->getRecord();

        return ucwords($record->user->name) . ' - ' . $record->email . ' Pet Owner';
    }

    // protected function getActions(): array
    // {
    //     return [
    //         \Filament\Actions\Action::make('back_to_list')
    //             ->label('Back to Appointments')
    //             ->icon('heroicon-o-arrow-left')
    //             ->color('primary')
    //             ->url(fn() => static::getResource()::getUrl()),

    //         Actions\ActionGroup::make([
    //             Actions\EditAction::make()->label('Edit Appointment')->icon('heroicon-o-pencil'),

    //             \Filament\Actions\Action::make('update_appointment_status')
    //                 ->label('Update Status')
    //                 ->icon('heroicon-o-wrench-screwdriver')
    //                 ->requiresConfirmation()
    //                 ->tooltip('Update Appointment status')
    //                 ->modalHeading(fn($record) => 'Confirm Status Update')
    //                 ->modalDescription(fn($record) => 'Are you sure you want to update the status of ' . ucwords($record->user->name) . '?')
    //                 ->color('warning')
    //                 ->modalSubmitActionLabel('Confirm Update')
    //                 ->modalWidth('2xl')
    //                 ->form([
    //                     \Filament\Forms\Components\Grid::make(2)
    //                         ->schema([
    //                             ToggleButtons::make('appointment_status')
    //                                 ->options(AppointmentStatusEnum::class)
    //                                 ->default(fn($record) => $record->appointment_status)
    //                                 ->dehydrated()
    //                                 ->inline()
    //                                 ->reactive()
    //                                 ->required()
    //                                 ->label('Appointment Status')
    //                                 ->columnSpan(2),


    //                             \Filament\Forms\Components\Select::make('approved_categories')
    //                                 ->label('Select Services to Approve')
    //                                 ->options(function ($record) {
    //                                     return $record->categories->pluck('appoint_cat_name', 'id')->toArray();
    //                                 })
    //                                 ->optionsLimit(5)
    //                                 ->default(fn($record) => $record->categories->pluck('id')->toArray()) // Select all by default
    //                                 ->hidden(
    //                                     fn(Get $get, $record) =>
    //                                     $get('appointment_status') !== 'approved' ||
    //                                         $record->categories->count() <= 1
    //                                 )
    //                                 ->multiple()
    //                                 ->preload()
    //                                 ->searchable()
    //                                 ->columns(2)
    //                                 ->columnSpan(2)
    //                                 ->required(),



    //                             DateTimePicker::make('appoint_sched')
    //                                 ->required()
    //                                 ->seconds(false)
    //                                 ->default(fn($record) => $record->appoint_sched ?? now()->startOfDay())
    //                                 ->minDate(now()->startOfDay())
    //                                 ->maxDate(now()->addWeek()->endOfDay())
    //                                 ->hidden(fn(Get $get) => $get('appointment_status') !== 'approved')
    //                                 ->dehydrated()
    //                                 ->label('Schedule Date')
    //                                 ->columnSpan(1),

    //                             ToggleButtons::make('payment_status')
    //                                 ->options(PaymentStatusEnum::class)
    //                                 ->default(fn($record) => $record->payment_status)
    //                                 ->dehydrated()
    //                                 ->inline()
    //                                 ->required()
    //                                 ->label('Payment Status')
    //                                 ->columnSpan(2), // Make this span both columns
    //                         ])

    //                 ])
    //                 ->action(function ($record, array $data) {
    //                     // $record->update([
    //                     //     'appointment_status' => $data['appointment_status'],
    //                     // ]);
    //                     $updateData = [
    //                         'appointment_status' => $data['appointment_status'],
    //                         'payment_status' => $data['payment_status'],
    //                         //'appoint_sched' => $data['appoint_sched'] ?? null,
    //                     ];

    //                     if ($data['appointment_status'] === 'approved' && isset($data['appoint_sched'])) {
    //                         $updateData['appoint_sched'] = $data['appoint_sched'];
    //                         // if ($record->categories->count() > 1 && isset($data['approved_categories'])) {
    //                         //     // Sync only the approved categories
    //                         //     $syncData = [];
    //                         //     foreach ($record->categories as $category) {
    //                         //         $syncData[$category->id] = [
    //                         //             'is_approved' => in_array($category->id, $data['approved_categories'])
    //                         //         ];
    //                         //     }
    //                         // } else {
    //                         //     foreach ($record->categories as $category) {
    //                         //         $syncData[$category->id] = ['is_approved' => true];
    //                         //     }
    //                         //     $record->categories()->sync($syncData);
    //                         // }
    //                     } else if ($data['appointment_status'] !== 'approved') {
    //                         // Keep the existing appoint_sched values
    //                         $updateData['appoint_sched'] = $record->appoint_sched;
    //                     }

    //                     // if($data['appointment_status'] === AppointmentStatusEnum::Approved) {
    //                     //     $updateData['appoint_sched'] = $data['appoint_sched'];
    //                     // }

    //                     $record->update($updateData);
    //                     Notification::make()
    //                         ->title('Updates Successfuly')
    //                         ->success()
    //                         ->send();
    //                 }),

    //         ])
    //     ];
    //}
}
