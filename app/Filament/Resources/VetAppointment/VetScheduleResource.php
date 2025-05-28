<?php

namespace App\Filament\Resources\VetAppointment;

use Closure;
use DateTimeZone;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use App\Models\Appointment\VetSchedule;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Widgets\VetinarySchedule;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VetAppointment\VetScheduleResource\Pages;
use App\Filament\Resources\VetAppointment\VetScheduleResource\RelationManagers;

class VetScheduleResource extends Resource
{
    protected static ?string $model = VetSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationLabel = 'Vetinary Schedule';
    protected static ?string $navigationGroup = 'Vetinary Appointment';
    //protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Section::make()
                ->schema([
                
           
                Hidden::make('user_id')
                ->default(auth()->user()->id),

                DateTimePicker::make('vet_schedule_open')
                ->required()
                //->displayFormat('F j, Y h:i A') //format full year, day, month, time
                ->rule('after_or_equal:now')
                 ->native(false)
                 ->seconds(false)
                ->date('F j, Y, g:i a')
                ->hint('Must be today or later')
                ->hintColor('warning')
                ->label('Opening Time'),

                DateTimePicker::make('vet_schedule_close')
                ->required()
                ->seconds(false)
                ->native(false) 
                  ->date('F j, Y, g:i a')
                // ->displayFormat('F j, Y h:i A')
                // ->rule(function ( $get) {
                //     $openTime = $get('vet_shedule_open');
                   
                //     //dapat ang closing sched is greater than sa opening sched pero nd sa dapat mag lapaw sa date subong
                //     return [
                //         'after:' . $openTime,
                //     ];
                // })
                ->rule(function ($get) {
                    $open = $get('vet_schedule_open');
            
                    return function ($attribute, $value, $fail) use ($open) {
                        if (!$open || !$value) return;
            
                        $openTime = Carbon::parse($open);
                        $closeTime = Carbon::parse($value);
                    //if ang user select data greater than the opening time throw error message
                        if ($openTime->toDateString() !== $closeTime->toDateString()) {
                            $fail('The closing time must be on the same date as the opening time.');
                            //if ang time is less than or equal sa opening time throw error message
                        } elseif ($closeTime->lessThanOrEqualTo($openTime)) {
                            $fail('The closing time must be after the opening time.');
                        }
                    };
                })
                ->hint('Must be after opening time, but not later than now.')
                ->hintColor('warning')
                ->label('Closing Time'),

                // ToggleButtons::make('is_the_same_schedule')
                // ->required()
                // ->boolean()
                // ->label('Is the same schedule every day?')
                // ->grouped()
                // ->colors([
                //     false => 'warning',
                //     true => 'success',
                // ])
                // ->icons([
                //     false => 'heroicon-m-x-circle',
                //     true => 'heroicon-m-check-circle',
                // ])
                // ->default(false),

                TextInput::make('num_customers')
                ->label('Enter the number of appointments you wish to accommodate')
                ->required()
                // ->columnSpanfull()
                ->rules([
                   'min:1',
                   'max:50',
                ])
                ->numeric(),
               
                
            ])->columns(2),

        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // Tables\Columns\TextColumn::make('user.name')
                // ->label('Vet Name')
                // ->sortable()
                // ->searchable(),

                Tables\Columns\TextColumn::make('vet_schedule_open')
                ->label('Opening Time')
                ->sortable()
                ->formatStateUsing(fn ($state) => date('F j, Y h:i A', strtotime($state))),

                Tables\Columns\TextColumn::make('vet_schedule_close')
                ->label('Closing Time')
                ->sortable()
                ->formatStateUsing(fn ($state) => date('F j, Y h:i A', strtotime($state))),

                // Tables\Columns\IconColumn::make('is_the_same_schedule')
                // ->boolean()
                // ->label('Is the same schedule every day?')
                // ->colors([
                //     false => 'warning',
                //     true => 'success',
                // ])
                // ->icons([
                //     false => 'heroicon-m-x-circle',
                //     true => 'heroicon-m-check-badge',
                // ]),

                TextColumn::make('num_customers')
                ->label('Number of Appointments to Accommodate')
                ->badge()
                //->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),

              
            ])
            ->filters([
               
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus-circle')
                ->label(__('Create Vetinary Schedule')),
            ])->emptyStateIcon('heroicon-s-calendar-date-range')
            ->emptyStateHeading('No Vetinary Schedules');
    }

    public static function getWidgets(): array
    {
        return [
            VetinarySchedule::class
        ];
    }   

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVetSchedules::route('/'),
            'create' => Pages\CreateVetSchedule::route('/create'),
            'edit' => Pages\EditVetSchedule::route('/{record}/edit'),
        ];
    }

    
    
}
