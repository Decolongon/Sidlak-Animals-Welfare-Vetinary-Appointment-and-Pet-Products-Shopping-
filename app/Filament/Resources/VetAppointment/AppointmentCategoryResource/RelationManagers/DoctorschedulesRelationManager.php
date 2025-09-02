<?php

namespace App\Filament\Resources\VetAppointment\AppointmentCategoryResource\RelationManagers;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Enums\DayEnum;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DoctorschedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'doctorschedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Availability Schedule')
                    ->description('Set your working days and hours')
                    ->schema([
                        Fieldset::make('Working Days')
                            ->schema([
                                CheckboxList::make('days')
                                    ->required()
                                    ->label('Select working days')
                                    ->options(DayEnum::class)
                                    ->columns(3)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                            ]),

                        Fieldset::make('Working Hours')
                            ->schema([
                                TimePicker::make('start_time')
                                    ->seconds(false)
                                    ->label('Start Time')
                                    ->required()
                                    ->live()
                                    ->prefixIcon('heroicon-m-clock')
                                    ->placeholder('Select start time'),

                                TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->seconds(false)
                                    ->required()
                                    ->prefixIcon('heroicon-m-clock')
                                    ->placeholder('Select end time')
                                    ->rules([
                                        function ($get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $startTime = $get('start_time');
                                                if ($value && $startTime && strtotime($value) <= strtotime($startTime)) {
                                                    $fail('End Time must be after Start Time.');
                                                }
                                            };
                                        }
                                    ]),
                            ]),

                        Fieldset::make('Schedule Validity')
                            ->schema([
                                DatePicker::make('effective_from')
                                    ->label('Effective From')
                                    ->minDate(now()->startOfDay())
                                    //->unique(ignoreRecord: true)
                                    ->required()
                                    ->live()
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->placeholder('Select start date')
                                    ->helperText('When this schedule becomes active'),

                                DatePicker::make('effective_to')
                                    ->label('Effective To')
                                    ->minDate(now()->startOfDay())
                                    ->required()
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->placeholder('Select end date')
                                    ->helperText('When this schedule expires')
                                    ->rules([
                                        function ($get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $effectiveFrom = $get('effective_from');
                                                if ($value && $effectiveFrom && strtotime($value) <= strtotime($effectiveFrom)) {
                                                    $fail('Effective To must be after Effective From.');
                                                }
                                            };
                                        }
                                    ])
                                    ->minDate(now()->startOfDay()),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('effective_from')
            ->columns([
                   TextColumn::make('days')
                    ->label('Days')
                    ->wrap()
                    ->html()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->formatStateUsing(fn($state) => ucwords($state))
                    ->badge(),

                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->formatStateUsing(fn($state) => date('h:i A', strtotime($state))),

                TextColumn::make('end_time')
                    ->label('End Time')
                    ->formatStateUsing(fn($state) => date('h:i A', strtotime($state))),

                TextColumn::make('effective_from')
                    ->label('Effective From')
                    ->formatStateUsing(fn($state) => date('F j, Y', strtotime($state))),

                TextColumn::make('effective_to')
                    ->label('Effective To')
                    ->formatStateUsing(fn($state) => date('F j, Y', strtotime($state))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
